<?php

namespace packages\userpanel\Controllers;

use packages\base;
use packages\base\InputValidationException;
use packages\base\IO\File;
use packages\base\Packages;
use packages\base\Translator;
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\Controller;
use packages\userpanel\Country;
use packages\userpanel\Events\Settings as SettingsEvent;
use packages\userpanel\Log;
use packages\userpanel\Logs;
use packages\userpanel\User\SocialNetwork;
use packages\userpanel\View;
use themes\clipone\Views;

class Profile extends Controller
{
    protected $authentication = true;

    public function view($data)
    {
        Authorization::haveOrFail('profile_view');
        $view = View::byName(Views\Profile\Index::class);
        $view->triggerTabs();
        $view->activeTab('view');
        $user = Authentication::getUser();
        $settingsEvent = new SettingsEvent();
        $settingsEvent->setUser($user);
        $settingsEvent->trigger();
        $view->setSettings($settingsEvent->get());
        $view->setUserData($user);
        $this->response->setStatus(true);
        $this->response->setView($view);

        return $this->response;
    }

    public function edit($data)
    {
        Authorization::haveOrFail('profile_edit');
        $types = Authorization::childrenTypes();
        $user = Authentication::getUser();
        $view = View::byName(Views\Profile\Index::class);
        $this->response->setView($view);
        $view->triggerTabs();
        $view->activeTab('edit');
        $view->setCountries(Country::get());
        $view->setUserData($user);
        $settingsEvent = new SettingsEvent();
        $settingsEvent->setUser($user);
        $settingsEvent->trigger();
        $view->setSettings($settingsEvent->get());
        $view->setForm();
        $this->response->setStatus(true);

        return $this->response;
    }

    public function update($data)
    {
        Authorization::haveOrFail('profile_edit');
        $types = Authorization::childrenTypes();
        $user = Authentication::getUser();
        $view = View::byName(Views\Profile\Index::class);
        $this->response->setView($view);
        $view->setDataForm($user->toArray());
        $view->triggerTabs();
        $view->activeTab('edit');
        $view->setCountries(Country::get());
        $view->setUserData($user);
        $settingsEvent = new SettingsEvent();
        $settingsEvent->setUser($user);
        $settingsEvent->trigger();
        $view->setSettings($settingsEvent->get());
        $view->setForm();

        $inputs = [
            'name' => [
                'optional' => true,
                'type' => 'string',
            ],
            'lastname' => [
                'optional' => true,
                'type' => 'string',
            ],
            'password' => [
                'optional' => true,
                'empty' => true,
            ],
            'zip' => [
                'optional' => true,
                'type' => 'string',
                'regex' => '/^([0-9]{5}|[0-9]{10})$/',
                'empty' => true,
            ],
            'city' => [
                'optional' => true,
                'type' => 'string',
            ],
            'country' => [
                'type' => Country::class,
                'optional' => true,
            ],
            'address' => [
                'optional' => true,
                'type' => 'string',
            ],
            'phone' => [
                'optional' => true,
                'type' => 'phone',
                'empty' => true,
            ],
            'avatar' => [
                'optional' => true,
                'type' => 'image',
            ],
            'avatar_remove' => [
                'optional' => true,
                'type' => 'bool',
            ],
            'socialnets' => [
                'optional' => true,
                'type' => function ($data, $rules, $input) {
                    if (!$data) {
                        return [];
                    }
                    if (!is_array($data)) {
                        throw new InputValidationException($input);
                    }

                    foreach ($data as $network => $url) {
                        $regex = '';
                        switch ($network) {
                            case SocialNetwork::telegram:
                                $regex = '/^(https?:\\/\\/(t(elegram)?\\.me|telegram\\.org)\\/)?(?<username>[a-z0-9\\_]{5,32})\\/?$/i';
                                break;
                            case SocialNetwork::instagram:
                                $regex = '/^(https?:\/\/(www\.)?instagram\.com\/)?(?<username>[A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/i';
                                break;
                            case SocialNetwork::skype:
                                $regex = '/^(?:(?:callto|skype):)?(?<username>(?:[a-z][a-z0-9\\.,\\-_]{5,31}))(?:\\?(?:add|call|chat|sendfile|userinfo))?$/i';
                                $site = 'skype:';
                                break;
                            case SocialNetwork::twitter:
                                $regex = '/^(?:https?:\/\/(?:.*\.)?twitter\.com\/)?(?<username>[A-z 0-9 _]+)\/?$/i';
                                break;
                            case SocialNetwork::facebook:
                                $regex = '/^(?:https?:\/\/(?:www\.)?(?:facebook|fb)\.com\/)?(?<username>[A-z 0-9 _ - \.]+)\/?$/i';
                                break;
                            case SocialNetwork::gplus:
                                $regex = '/^(?:https?:\/\/plus\.google\.com\/)?(?<username>(?:\+[a-z0-9\_]+|\d{21}))\/?$/i';
                                break;
                            default:
                                throw new InputValidationException($input."[{$network}]");
                        }
                        if ($url) {
                            if (preg_match($regex, $url, $matches)) {
                                $data[$network] = $matches['username'];
                            } else {
                                throw new InputValidationException($input."[{$network}]");
                            }
                        }
                    }

                    return $data;
                },
            ],
        ];
        if (Authorization::is_accessed('profile_edit_privacy')) {
            foreach ([
                'email',
                'cellphone',
                'phone',
                'socialnetworks_'.SocialNetwork::telegram,
                'socialnetworks_'.SocialNetwork::twitter,
                'socialnetworks_'.SocialNetwork::instagram,
                'socialnetworks_'.SocialNetwork::facebook,
                'socialnetworks_'.SocialNetwork::skype,
                'socialnetworks_'.SocialNetwork::gplus,
            ] as $visibility) {
                $inputs['visibility_'.$visibility] = [
                    'optional' => true,
                    'type' => 'bool',
                    'empty' => true,
                ];
            }
        }
        $formdata = $this->checkinputs($inputs);
        if (array_key_exists('avatar', $formdata) and !$formdata['avatar']) {
            unset($formdata['avatar']);
        }
        if (isset($formdata['zip']) and !$formdata['zip']) {
            $formdata['zip'] = null;
        }
        $logsfeilds = [
            'name',
            'lastname',
            'password',
            'zip',
            'city',
            'country',
            'address',
            'phone',
            'avatar',
        ];
        $old = [];
        foreach ($logsfeilds as $field) {
            $old[$field] = $user->original_data[$field];
        }
        if (isset($formdata['avatar_remove']) and $formdata['avatar_remove']) {
            $formdata['avatar'] = null;
        } elseif (isset($formdata['avatar'])) {
            $tmpfile = new File\TMP();
            $formdata['avatar']->resize(200, 200)->saveToFile($tmpfile);
            $formdata['avatar'] = 'storage/public_avatar/'.$tmpfile->md5().'.'.$formdata['avatar']->getExtension();
            $avatar = new File\Local(Packages::package('userpanel')->getFilePath($formdata['avatar']));
            $directory = $avatar->getDirectory();
            if (!$directory->exists()) {
                $directory->make(true);
            }
            $tmpfile->copyTo($avatar);
        }
        if (isset($formdata['password']) and $formdata['password']) {
            $user->password_hash($formdata['password']);
        }
        unset($formdata['password']);
        $user->save($formdata);
        unset($formdata['avatar']);
        if (isset($formdata['socialnets'])) {
            $findSocialNetwork = function ($network) use (&$user) {
                foreach ($user->socialnetworks as $socialnet) {
                    if ($socialnet->network == $network) {
                        return $socialnet;
                    }
                }

                return null;
            };
            foreach ($formdata['socialnets'] as $network => $username) {
                $socialnet = $findSocialNetwork($network);
                if ($username) {
                    if ($socialnet) {
                        $socialnet->username = $username;
                        $socialnet->save();
                    } else {
                        $socialnet = new SocialNetwork();
                        $socialnet->user = $user->id;
                        $socialnet->network = $network;
                        $socialnet->username = $username;
                        $socialnet->save();
                    }
                } elseif ($socialnet) {
                    $socialnet->delete();
                }
            }
        }
        $inputs = [
            'oldData' => [],
            'newData' => [],
        ];
        if (Authorization::is_accessed('profile_edit_privacy')) {
            $visibilities = $user->getOption('visibilities');
            if (!is_array($visibilities)) {
                $visibilities = [];
            }
            $inputs['oldData']['visibilities'] = $visibilities;
            foreach ([
                'email',
                'cellphone',
                'phone',
                'socialnetworks_'.SocialNetwork::telegram,
                'socialnetworks_'.SocialNetwork::instagram,
                'socialnetworks_'.SocialNetwork::skype,
                'socialnetworks_'.SocialNetwork::twitter,
                'socialnetworks_'.SocialNetwork::facebook,
                'socialnetworks_'.SocialNetwork::gplus,
            ] as $field) {
                if (array_key_exists('visibility_'.$field, $formdata)) {
                    if ($formdata['visibility_'.$field]) {
                        $visibilities[] = $field;
                    } elseif (($key = array_search($field, $visibilities)) !== false) {
                        unset($visibilities[$key]);
                    }
                }
            }
            $visibilities = array_values(array_unique($visibilities));
            $user->setOption('visibilities', $visibilities);
            $inputs['newData']['visibilities'] = $visibilities;
        }
        foreach ($old as $field => $val) {
            if ($val != $user->original_data[$field]) {
                $inputs['oldData'][$field] = $val;
                $inputs['newData'][$field] = $user->$field;
            }
        }
        if (isset($inputs['oldData']['password'])) {
            $inputs['oldData']['password'] = '***';
        }
        if (isset($inputs['newData']['password'])) {
            $inputs['newData']['password'] = '***';
        }
        $log = new Log();
        $log->title = t('log.profileEdit');
        $log->type = Logs\UserEdit::class;
        $log->user = $user->id;
        $log->parameters = $inputs;
        $log->save();

        $this->response->setStatus(true);

        return $this->response;
    }

    public function settings($data)
    {
        Authorization::haveOrFail('profile_settings');
        $settingsEvent = new SettingsEvent();
        $user = Authentication::getUser();
        $settingsEvent->setUser($user);
        $settingsEvent->trigger();
        if (!$settingsEvent->get()) {
            throw new base\NotFound();
        }
        $view = View::byName(Views\Profile\Index::class);
        $view->triggerTabs();
        $view->activeTab('settings');
        $view->setUser($user);
        $view->setSettings($settingsEvent->get());
        $this->response->setStatus(true);
        $this->response->setView($view);

        return $this->response;
    }

    public function change($data)
    {
        Authorization::haveOrFail('profile_settings');
        $settingsEvent = new SettingsEvent();
        $user = Authentication::getUser();
        $settingsEvent->setUser($user);
        $settingsEvent->trigger();
        $settings = $settingsEvent->get();
        if (!$settings) {
            throw new base\NotFound();
        }
        $view = View::byName(Views\Profile\Index::class);
        $view->triggerTabs();
        $view->activeTab('settings');
        $this->response->setView($view);
        $view->setUser($user);
        $view->setSettings($settings);
        $this->response->setStatus(true);
        $inputsRules = [];
        $logs = [];
        foreach ($settings as $setting) {
            if ($SRules = $setting->getInputs()) {
                $SRules = $inputsRules = array_merge($inputsRules, $SRules);
                $ginputs = $this->checkinputs($SRules);
                $logs = array_merge($logs, $setting->store($ginputs, $user));
            }
        }
        $view->setDataForm($this->inputsvalue($inputsRules));
        if ($logs) {
            $inputs = [
                'oldData' => [],
                'newData' => [],
            ];
            foreach ($logs as $log) {
                $inputs['oldData'][$log->getName()] = ['title' => $log->getTitle(), 'value' => $log->getOldValue()];
                $inputs['newData'][$log->getName()] = ['title' => $log->getTitle(), 'value' => $log->getNewValue()];
            }
            $log = new Log();
            $log->title = t('log.profileEdit');
            $log->type = Logs\UserEdit::class;
            $log->user = $user->id;
            $log->parameters = $inputs;
            $log->save();
        }

        return $this->response;
    }
}
