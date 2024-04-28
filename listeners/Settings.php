<?php

namespace packages\userpanel\listeners;

use packages\base\Options;
use packages\userpanel\Controllers\Settings as Controller;
use packages\userpanel\Events\General\Settings as SettingsEvent;
use packages\userpanel\Register\RegisterField;
use packages\userpanel\Register\RegisterFields;
use packages\userpanel\User;
use packages\userpanel\UserType;

class Settings
{
    public function init(SettingsEvent $settings)
    {
        $setting = new SettingsEvent\Setting('userpanel');
        $setting->setController(Controller::class);
        $setting->setIcon('fa fa-users');
        $this->addRegisterItems($setting);
        $settings->addSetting($setting);
    }

    private function addRegisterItems(SettingsEvent\Setting $setting)
    {
        $setting->addInput([
            'name' => 'userpanel_register_enabled',
            'type' => 'bool',
        ]);
        $setting->addInput([
            'name' => 'userpanel_register_type',
            'type' => UserType::class,
        ]);
        $setting->addInput([
            'name' => 'userpanel_register_status',
            'type' => 'int8',
            'zero' => true,
            'values' => [
                User::active,
                User::deactive,
                User::suspend,
            ],
        ]);
        $setting->addField([
            'name' => 'userpanel_register_enabled',
            'type' => 'radio',
            'label' => t('settings.userpanel.register'),
            'inline' => true,
            'options' => [
                [
                    'label' => t('active'),
                    'value' => 1,
                ],
                [
                    'label' => t('deactive'),
                    'value' => 0,
                ],
            ],
        ]);
        $setting->addField([
            'name' => 'userpanel_register_type',
            'type' => 'select',
            'label' => t('settings.userpanel.register.usertype'),
            'options' => $this->getUserTypesForSelect(),
        ]);
        $setting->addField([
            'name' => 'userpanel_register_status',
            'type' => 'select',
            'label' => t('settings.userpanel.register.status'),
            'options' => $this->getUserStatusForSelect(),
        ]);

        $setting->addInput([
            'name' => 'userpanel_register_crediential',
            'type' => 'string',
            'values' => [
                RegisterField::EMAIL->value,
                RegisterField::CELLPHONE->value,
                implode(',', [RegisterField::EMAIL->value, RegisterField::CELLPHONE->value]),
            ],
        ]);

        $registerFieldRules = [];
        foreach (RegisterFields::all() as $field) {
            $registerFieldRules[$field->value] = [
                'type' => 'number',
                'zero' => true,
                'optional' => true,
                'values' => [
                    RegisterFields::DEACTIVE,
                    RegisterFields::ACTIVE_REQUIRED,
                    RegisterFields::ACTIVE_OPTIONAL,
                ],
            ];
        }
        $setting->addInput([
            'name' => 'userpanel_register_fields',
            'type' => 'array',
            'assoc' => true,
            'duplicate' => 'keep',
            'rules' => $registerFieldRules,
        ]);

        $setting->addInput([
            'name' => 'userpanel_register_crediential',
            'type' => 'string',
            'optional' => true,
            'values' => [
                RegisterField::EMAIL->value,
                RegisterField::CELLPHONE->value,
                implode('|', [RegisterField::EMAIL->value, RegisterField::CELLPHONE->value]),
            ],
        ]);

        $setting->addField([
            'name' => 'userpanel_register_crediential',
            'type' => 'select',
            'label' => t('packages.userpanel.settings.register.field.label.credientials'),
            'options' => [
                [
                    'title' => t('packages.userpanel.settings.register.field.email'),
                    'value' => RegisterField::EMAIL->value,
                ],
                [
                    'title' => t('packages.userpanel.settings.register.field.cellphone'),
                    'value' => RegisterField::CELLPHONE->value,
                ],
                [
                    'title' => t('packages.userpanel.settings.register.field.email_and_cellphone'),
                    'value' => implode('|', [RegisterField::EMAIL->value, RegisterField::CELLPHONE->value]),
                ],
            ],
        ]);

        foreach (RegisterFields::all() as $field) {
            $setting->addField([
                'name' => "userpanel_register_fields[{$field->value}]",
                'type' => 'select',
                'label' => t("packages.userpanel.settings.register.field.label.{$field->value}"),
                'value' => $field->isDeactivated() ?
                    RegisterFields::DEACTIVE : ($field->isRequired() ?
                        RegisterFields::ACTIVE_REQUIRED :
                        RegisterFields::ACTIVE_OPTIONAL
                    ),
                'options' => $this->getUserAttributeOptionsForSelect(),
            ]);
        }
        $options = Options::get('packages.userpanel.register');
        $setting->setDataForm('userpanel_register_enabled', (isset($options['enable']) and $options['enable']) ? 1 : 0);
        $setting->setDataForm('userpanel_register_type', $options['type']);
        $setting->setDataForm('userpanel_register_status', $options['status'] ?? User::active);
        $setting->setDataForm('userpanel_register_crediential', $options['register_crediential'] ?? '');
    }

    private function getUserAttributeOptionsForSelect(): array
    {
        return [
            [
                'title' => t('deactive'),
                'value' => RegisterFields::DEACTIVE,
            ],
            [
                'title' => t('required'),
                'value' => RegisterFields::ACTIVE_REQUIRED,
            ],
            [
                'title' => t('optional'),
                'value' => RegisterFields::ACTIVE_OPTIONAL,
            ],
        ];
    }

    private function getUserTypesForSelect(): array
    {
        $options = [];
        foreach (UserType::get() as $type) {
            $options[] = [
                'title' => $type->title,
                'value' => $type->id,
            ];
        }

        return $options;
    }

    private function getUserStatusForSelect(): array
    {
        return [
            [
                'title' => t('active'),
                'value' => User::active,
            ],
            [
                'title' => t('deactive'),
                'value' => User::deactive,
            ],
            [
                'title' => t('suspend'),
                'value' => User::suspend,
            ],
        ];
    }
}
