<?php
namespace packages\userpanel\listeners;

use packages\base\{Date, Options};
use packages\userpanel\{events\General\Settings as SettingsEvent, controllers\Settings as Controller, Usertype, User};

class Settings {
	
	public function init(SettingsEvent $settings){
		$setting = new SettingsEvent\Setting('userpanel');
		$setting->setController(Controller::class);
		$this->addRegisterItems($setting);
		$settings->addSetting($setting);
	}

	private function addRegisterItems(SettingsEvent\Setting $setting) {
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
		$options = Options::get("packages.userpanel.register");
		$setting->setDataForm('userpanel_register_enabled', (isset($options["enable"]) and $options["enable"]) ? 1 : 0);
		$setting->setDataForm('userpanel_register_type', $options["type"]);
		$setting->setDataForm('userpanel_register_status', $options["status"] ?? User::active);
	}
	private function getUserTypesForSelect(): array {
		$options = array();
		foreach (Usertype::get() as $type) {
			$options[] = array(
				"title" => $type->title,
				"value" => $type->id,
			);
		}
		return $options;
	}
	private function getUserStatusForSelect(): array {
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
