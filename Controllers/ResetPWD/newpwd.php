<?php

namespace packages\userpanel\controllers\ResetPWD;

use packages\base\{InputValidationException};
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\Controller;
use packages\userpanel\Log;
use packages\userpanel\Logs;
use packages\userpanel\View;
use packages\userpanel\Views;

use function packages\userpanel\Url;

class NewPWD extends Controller
{
    protected $authentication = true;

    public function edit()
    {
        Authorization::haveOrFail('resetpwd_newpwd');
        $view = View::byName(Views\ResetPWD\NewPWD::class);
        $this->response->setStatus(true);
        $this->response->setView($view);

        return $this->response;
    }

    public function update()
    {
        Authorization::haveOrFail('resetpwd_newpwd');
        $view = View::byName(Views\ResetPWD\NewPWD::class);
        $this->response->setView($view);

        $inputs = $this->checkinputs([
            'password' => [
                'type' => 'string',
            ],
            'password2' => [
                'type' => 'string',
            ],
        ]);

        if ($inputs['password'] != $inputs['password2']) {
            throw new InputValidationException('dontmatch');
        }

        $user = Authentication::getUser();
        $user->password_hash($inputs['password']);
        $user->save();

        $log = new Log();
        $log->title = t('log.resetPWD');
        $log->type = Logs\UserEdit::class;
        $log->user = $user->id;
        $log->parameters = [
            'oldData' => [
                'password' => '***',
            ],
        ];
        $log->save();

        $this->response->setStatus(true);
        $this->response->Go(url('/'));

        return $this->response;
    }
}
