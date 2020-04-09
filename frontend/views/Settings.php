<?php
namespace themes\clipone\views;
use packages\userpanel;
use packages\userpanel\{views\Form, Authorization};
use themes\clipone\{Navigation, ViewTrait, views\FormTrait};

class Settings extends Form {
	use ViewTrait, FormTrait;

	public static function onSourceLoad() {
		if (Authorization::is_accessed("settings_general-settings")) {
			$settings = dashboard::getSettingsMenu();
			$item = new Navigation\MenuItem("userpanel_general-settings");
			$item->setTitle(t('userpanel.general-settings'));
			$item->setURL(userpanel\url('settings'));
			$item->setIcon('fa fa-cogs');
			$settings->addItem($item);
		}
	}

	private $settings = array();
	public function __beforeLoad(){
		$this->setTitle(t('userpanel.general-settings'));
		$this->addBodyClass('userpanel-settings');
		$this->addBodyClass('userpanel-general-settings');
		navigation::active("settings/userpanel_general-settings");
		$this->initFormData();
	}
	public function setSettings(array $settings) {
		$this->settings = $settings;
	}
	protected function getSettings(): array {
		return $this->settings;
	}
	private function initFormData() {
		foreach ($this->getSettings() as $setting) {
			foreach ($setting->getInputs() as $input) {
				$value = $setting->getDataForm($input['name']);
				if ($value !== null) {
					$this->setDataForm($value, $input['name']);
				}
			}
		}
	}
}
