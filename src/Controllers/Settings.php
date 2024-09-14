<?php

namespace packages\userpanel\Controllers;

use packages\base\NotFound;
use packages\base\Options;
use packages\base\View;
use packages\userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\Events\General\Settings as Event;
use packages\userpanel\Events\General\Settings\Controller;
use packages\userpanel\Events\General\Settings\Log;
use packages\userpanel\Logs;
use packages\userpanel\Register\RegisterField;
use packages\userpanel\Register\RegisterFields;
use packages\userpanel\User;
use packages\userpanel\UserType;
use themes\clipone\Views;

class Settings extends userpanel\Controller implements Controller
{
    protected $authentication = true;

    public function view()
    {
        Authorization::haveOrFail('settings_general-settings');
        $event = new Event();
        $event->trigger();
        if (!$event->get()) {
            throw new NotFound();
        }
        $view = View::byName(Views\Settings::class);
        $view->setSettings($event->get());
        $this->response->setView($view);

        return $this->response;
    }

    public function update()
    {
        Authorization::haveOrFail('settings_general-settings');
        $event = new Event();
        $event->trigger();
        $settings = $event->get();
        if (!$settings) {
            throw new NotFound();
        }
        $view = View::byName(Views\Settings::class);
        $view->setSettings($settings);
        $this->response->setView($view);
        $this->response->setStatus(false);
        $inputsRules = [];
        $logs = [];
        foreach ($settings as $setting) {
            if ($SRules = $setting->getInputs()) {
                $SRules = $inputsRules = array_merge($inputsRules, $SRules);
                $ginputs = $this->checkinputs($SRules);
                $logs = array_merge($logs, $setting->store($ginputs));
            }
        }
        $view->setDataForm($this->inputsvalue($inputsRules));
        $this->response->setStatus(true);
        $inputs = [
            'oldData' => [],
            'newData' => [],
        ];
        foreach ($logs as $log) {
            $inputs['oldData'][$log->getName()] = ['title' => $log->getTitle(), 'value' => $log->getOldValue()];
            $inputs['newData'][$log->getName()] = ['title' => $log->getTitle(), 'value' => $log->getNewValue()];
        }
        if ($logs) {
            $log = new userpanel\Log();
            $log->title = t('log.settings.general-settings.update');
            $log->type = Logs\Settings::class;
            $log->user = Authentication::getUser();
            $log->parameters = $inputs;
            $log->save();
        }

        return $this->response;
    }

    public function store(array $inputs): array
    {
        $logs = [];
        $options = Options::get('packages.userpanel.register');

        if (isset($inputs['userpanel_register_enabled'])) {
            if (!isset($options['enable']) or $options['enable'] != $inputs['userpanel_register_enabled']) {
                $logs[] = new Log('userpanel_register_enabled', $options['enable'] ? t('active') : t('deactive'), $inputs['userpanel_register_enabled'] ? t('active') : t('deactive'), t('setting.userpanel.register.enabled'));
                $options['enable'] = $inputs['userpanel_register_enabled'];
            }
        }

        if (isset($inputs['userpanel_register_type'])) {
            if (!isset($options['type']) or $options['type'] != $inputs['userpanel_register_type']->id) {
                $type = null;
                if (isset($options['type'])) {
                    $type = (new UserType())->byId($options['type']);
                }
                $oldValue = $options['type'] ?? '-';
                if ($type) {
                    $oldValue = $type->title;
                }
                $logs[] = new Log('userpanel_register_type', $oldValue, $inputs['userpanel_register_type']->title, t('settings.userpanel.register.usertype'));
                $options['type'] = $inputs['userpanel_register_type']->id;
            }
        }

        if (isset($inputs['userpanel_register_status'])) {
            $getStatusTitle = function (int $status) {
                switch ($status) {
                    case User::active:
                        return t('active');
                    case User::deactive:
                        return t('deactive');
                    case User::suspend:
                        return t('suspend');
                }
            };
            $logs[] = new Log('userpanel_register_type', (isset($options['status']) and $options['status']) ? $getStatusTitle($options['status']) : '-', $getStatusTitle($inputs['userpanel_register_status']), t('settings.userpanel.register.status'));
            $options['status'] = $inputs['userpanel_register_status'];
        }

        if (isset($inputs['userpanel_register_crediential'])) {
            $inputs['userpanel_register_fields'] ??= [];

            if (RegisterField::CELLPHONE->value == $inputs['userpanel_register_crediential']) {
                $inputs['userpanel_register_fields'][RegisterField::CELLPHONE->value] = RegisterFields::ACTIVE_REQUIRED;
            } elseif (RegisterField::EMAIL->value == $inputs['userpanel_register_crediential']) {
                $inputs['userpanel_register_fields'][RegisterField::EMAIL->value] = RegisterFields::ACTIVE_REQUIRED;
            } else {
                $inputs['userpanel_register_fields'][RegisterField::EMAIL->value] = RegisterFields::ACTIVE_REQUIRED;
                $inputs['userpanel_register_fields'][RegisterField::CELLPHONE->value] = RegisterFields::ACTIVE_REQUIRED;
            }

            $getStatusTitle = function (string $status) {
                switch ($status) {
                    case RegisterField::CELLPHONE->value:
                        return t('packages.userpanel.settings.register.field.cellphone');
                    case RegisterField::EMAIL->value:
                        return t('packages.userpanel.settings.register.field.cellphone');
                    case implode('|', [RegisterField::EMAIL->value, RegisterField::CELLPHONE->value]):
                        return t('packages.userpanel.settings.register.field.email_and_cellphone');
                    default: return '-';
                }
            };
            $logs[] = new Log(
                'userpanel_register_crediential',
                (isset($options['register_crediential']) and $options['register_crediential']) ? $getStatusTitle($options['register_crediential']) : '-',
                $getStatusTitle($inputs['userpanel_register_crediential']),
                t('packages.userpanel.settings.register.field.label.credientials')
            );
            $options['register_crediential'] = $inputs['userpanel_register_crediential'];
        }

        if (isset($inputs['userpanel_register_fields'])) {
            $getFieldsTitles = function (array $fields): string {
                $result = '';
                if ($fields) {
                    $result .= '<ul>';
                }
                foreach ($fields as $field => $status) {
                    $result .= '<li>'.
                        t("packages.userpanel.settings.register.field.{$field}").': '.(
                            RegisterFields::DEACTIVE == $status ? t('deactive') : (
                                RegisterFields::ACTIVE_OPTIONAL == $status ? t('optional') : (
                                    RegisterFields::ACTIVE_REQUIRED == $status ? t('required') : '-'
                                )
                            )
                        ).
                    '</li>';
                    $result .= PHP_EOL;
                }
                if ($fields) {
                    $result .= '</ul>';
                }

                return $result;
            };

            $logs[] = new Log(
                'userpanel_register_fields',
                (isset($options['register_fields']) and $options['register_fields']) ? $getFieldsTitles($options['register_fields']) : '-',
                $getFieldsTitles($inputs['userpanel_register_fields']),
                t('settings.userpanel.register.fields')
            );
            $options['register_fields'] = $inputs['userpanel_register_fields'];
        }

        Options::save('packages.userpanel.register', $options, true);

        return $logs;
    }
}
