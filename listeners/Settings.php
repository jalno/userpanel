<?php

namespace packages\userpanel\listeners;

use packages\base\{Date, Options};
use packages\userpanel\{events\General\Settings as SettingsEvent, controllers\Settings as Controller, Usertype, User};
use packages\userpanel\Register\RegisterField;
use packages\userpanel\Register\RegisterFields;

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
		$setting->addInput(array(
			'name' => 'userpanel_register_enabled',
			'type' => 'bool',
		));
		$setting->addInput(array(
			'name' => 'userpanel_register_type',
			'type' => Usertype::class,
		));
		$setting->addInput(array(
			'name' => 'userpanel_register_status',
			'type' => 'int8',
			'zero' => true,
			'values' => array(
				User::active,
				User::deactive,
				User::suspend,
			),
		));
		$setting->addField(array(
			'name' => 'userpanel_register_enabled',
			'type' => 'radio',
			'label' => t('settings.userpanel.register'),
			'inline' => true,
			'options' => array(
				array(
					'label' => t('active'),
					'value' => 1,
				),
				array(
					'label' => t('deactive'),
					'value' => 0,
				),
			),
		));
		$setting->addField(array(
			'name' => 'userpanel_register_type',
			'type' => 'select',
			'label' => t('settings.userpanel.register.usertype'),
			'options' => $this->getUserTypesForSelect(),
		));
		$setting->addField(array(
			'name' => 'userpanel_register_status',
			'type' => 'select',
			'label' => t('settings.userpanel.register.status'),
			'options' => $this->getUserStatusForSelect(),
		));

		$setting->addInput(array(
			'name' => 'userpanel_register_crediential',
			'type' => 'string',
			'values' => array(
				RegisterField::EMAIL->value,
				RegisterField::CELLPHONE->value,
				implode(',', [RegisterField::EMAIL->value, RegisterField::CELLPHONE->value]),
			),
		));

		$registerFieldRules = array();
		foreach (RegisterFields::all() as $field) {
			$registerFieldRules[$field->value] = array(
				'type' => 'number',
				'zero' => true,
				'optional' => true,
				'values' => array(
					RegisterFields::DEACTIVE,
					RegisterFields::ACTIVE_REQUIRED,
					RegisterFields::ACTIVE_OPTIONAL,
				),
			);
		}
		$setting->addInput(array(
			'name' => 'userpanel_register_fields',
			'type' => 'array',
			'assoc' => true,
			'duplicate' => 'keep',
			'rules' => $registerFieldRules,
		));

		$setting->addInput(array(
			'name' => 'userpanel_register_crediential',
			'type' => 'string',
			'optional' => true,
			'values' => array(
				RegisterField::EMAIL->value,
				RegisterField::CELLPHONE->value,
				implode('|', [RegisterField::EMAIL->value, RegisterField::CELLPHONE->value]),
			),
		));

		$setting->addField(array(
			'name' => "userpanel_register_crediential",
			'type' => 'select',
			'label' => t("packages.userpanel.settings.register.field.label.credientials"),
			'options' => array(
				array(
					"title" => t("packages.userpanel.settings.register.field.email"),
					"value" => RegisterField::EMAIL->value,
				),
				array(
					"title" => t("packages.userpanel.settings.register.field.cellphone"),
					"value" => RegisterField::CELLPHONE->value,
				),
				array(
					"title" => t("packages.userpanel.settings.register.field.email_and_cellphone"),
					"value" => implode('|', [RegisterField::EMAIL->value, RegisterField::CELLPHONE->value]),
				),
			),
		));

		foreach (RegisterFields::all() as $field) {
			$setting->addField(array(
				'name' => "userpanel_register_fields[{$field->value}]",
				'type' => 'select',
				'label' => t("packages.userpanel.settings.register.field.label.{$field->value}"),
				'value' => $field->isDeactivated() ?
					RegisterFields::DEACTIVE : ($field->isRequired() ?
						RegisterFields::ACTIVE_REQUIRED :
						RegisterFields::ACTIVE_OPTIONAL
					),
				'options' => $this->getUserAttributeOptionsForSelect(),
			));
		}
		$options = Options::get("packages.userpanel.register");
		$setting->setDataForm('userpanel_register_enabled', (isset($options["enable"]) and $options["enable"]) ? 1 : 0);
		$setting->setDataForm('userpanel_register_type', $options["type"]);
		$setting->setDataForm('userpanel_register_status', $options["status"] ?? User::active);
		$setting->setDataForm('userpanel_register_crediential', $options["register_crediential"] ?? '');
	}

	private function getUserAttributeOptionsForSelect(): array
	{
		return array(
			array(
				"title" => t("deactive"),
				"value" => RegisterFields::DEACTIVE,
			),
			array(
				"title" => t("required"),
				"value" => RegisterFields::ACTIVE_REQUIRED,
			),
			array(
				"title" => t("optional"),
				"value" => RegisterFields::ACTIVE_OPTIONAL,
			),
		);
	}

	private function getUserTypesForSelect(): array
	{
		$options = array();
		foreach (Usertype::get() as $type) {
			$options[] = array(
				"title" => $type->title,
				"value" => $type->id,
			);
		}
		return $options;
	}

	private function getUserStatusForSelect(): array
	{
		return array(
			array(
				"title" => t("active"),
				"value" => User::active,
			),
			array(
				"title" => t("deactive"),
				"value" => User::deactive,
			),
			array(
				"title" => t("suspend"),
				"value" => User::suspend,
			),
		);
	}
}
