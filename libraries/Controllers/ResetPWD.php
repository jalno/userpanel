<?php

namespace packages\userpanel\Controllers;

use packages\base\HTTP;
use packages\base\InputValidationException;
use packages\base\Options;
use packages\base\Response;
use packages\base\View;
use packages\notifications\API as NotificationAPI;
use packages\notifications\IChannel;
use packages\userpanel\Authentication;
use packages\userpanel\Controller;
use packages\userpanel\Date;
use packages\userpanel\Events;
use packages\userpanel\ResetPWD\Token;
use packages\userpanel\Validators;
use packages\userpanel\Views;

use function packages\userpanel\Url;

class ResetPWD extends Controller
{
    /**
     * @var bool
     */
    protected $authentication = false;

    /**
     * @var IChannel[]|null
     */
    protected $validChannels;

    protected Authentication\BruteForceThrottle $bruteForceThrottle;

    public function __construct()
    {
        parent::__construct();

        /** @var false|array{'period'?:int,'count'?:int}|null $throttleOptions */
        $throttleOptions = Options::get('packages.userpanel.login_and_reset_password.bruteforce_throttle');
        $this->bruteForceThrottle = new Authentication\BruteForceThrottle(
            'login-and-reset-password',
            intval($throttleOptions['period'] ?? 3600),
            intval($throttleOptions['total-limit'] ?? 7),
            intval($throttleOptions['session-limit'] ?? 0),
            null,
            null,
            [
                'ignore-ips' => (isset($throttleOptions['ignore-ips']) and is_array($throttleOptions['ignore-ips'])) ? $throttleOptions['ignore-ips'] : [],
            ]
        );
    }

    public function view(): Response
    {
        $view = View::byName(Views\ResetPWD::class);
        $view->setData($this->getChannelsNames(), 'channelsnames');
        $this->response->setView($view);
        $this->bruteForceThrottle->mustHasChance(true);

        $inputs = $this->checkInputs([
            'credential' => [
                'type' => 'string',
                'optional' => true,
            ],
            'method' => [
                'type' => 'string',
                'values' => $this->getChannelsNames(),
                'optional' => true,
            ],
        ]);
        foreach ($inputs as $key => $value) {
            $view->setDataForm($value, $key);
        }

        return $this->response;
    }

    public function reset(): Response
    {
        $view = View::byName(Views\ResetPWD::class);
        $view->setData($this->getChannelsNames(), 'channelsnames');
        $this->response->setView($view);

        $inputsRules = [
            'credential' => [
                'type' => Validators\UserCredentialValidator::class,
            ],
            'method' => [
                'type' => 'string',
                'values' => $this->getChannelsNames(),
            ],
        ];
        $view->setDataForm($this->inputsvalue($inputsRules));
        (new Events\BeforeResetPassword())->trigger();

        $this->bruteForceThrottle->mustHasChance();
        try {
            $inputs = $this->checkInputs($inputsRules);
        } catch (InputValidationException $e) {
            if ('credential' == $e->getInput()) {
                $this->bruteForceThrottle->loseOneChance();
            }
            throw $e;
        }

        $channel = $this->getChannelByName($inputs['method']);
        $token = new Token();
        $token->token = rand(1000, 999999);
        $token->user = $inputs['credential']->id;
        $token->ip = HTTP::$client['ip'];
        $channel->notify(new Events\ResetPWD($token));
        $token->sent_at = Date::time();
        $token->save();
        $this->response->setStatus(true);

        return $this->response;
    }

    public function token(): Response
    {
        $view = View::byName(Views\ResetPWD::class);
        $this->response->setView($view);
        $this->bruteForceThrottle->mustHasChance();
        $inputs = $this->checkInputs([
            'token' => [
                'type' => 'number',
            ],
            'credential' => [
                'type' => Validators\UserCredentialValidator::class,
            ],
        ]);

        $token = (new Token())
            ->where('user', $inputs['credential']->id)
            ->where('sent_at', Date::time() - 7200, '>')
            ->where('token', $inputs['token'])
            ->orderBy('sent_at', 'DESC')
            ->getOne();
        if (!$token) {
            $this->bruteForceThrottle->loseOneChance();
            throw new InputValidationException('token');
        }

        Login::doLogin($inputs['credential']);
        $token->delete();

        $this->response->setStatus(true);
        $this->response->Go(url('resetpwd/newpwd'));

        return $this->response;
    }

    /**
     * @return IChannel[]
     */
    private function getChannels(): array
    {
        if (null === $this->validChannels) {
            $event = new Events\ResetPWD(new Token());
            $this->validChannels = [];
            foreach (NotificationAPI::getChannels() as $channel) {
                if ($channel->canNotify($event)) {
                    $this->validChannels[] = $channel;
                }
            }
        }

        return $this->validChannels;
    }

    private function getChannelByName(string $name): ?IChannel
    {
        foreach ($this->getChannels() as $channel) {
            if ($channel->getName() == $name) {
                return $channel;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function getChannelsNames(): array
    {
        return array_map(fn ($channel) => $channel->getName(), $this->getChannels());
    }
}
