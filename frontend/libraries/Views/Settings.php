<?php

namespace themes\clipone\Views;

use packages\userpanel;
use packages\userpanel\Authorization;
use packages\userpanel\Events\General\Settings\Setting;
use packages\userpanel\Views\Form;
use themes\clipone\Navigation;
use themes\clipone\ViewTrait;

class Settings extends Form
{
    use ViewTrait;
    use FormTrait;

    private $settings = [];

    public function __beforeLoad()
    {
        $this->setTitle(t('userpanel.general-settings'));
        $this->addBodyClass('userpanel-settings');
        $this->addBodyClass('userpanel-general-settings');
        Navigation::active('settings/userpanel_general-settings');
        $this->initFormData();
    }

    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return Setting[]
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    private function initFormData()
    {
        foreach ($this->getSettings() as $setting) {
            foreach ($setting->getInputs() as $input) {
                $value = $setting->getDataForm($input['name']);
                if (null !== $value) {
                    $this->setDataForm($value, $input['name']);
                }
            }
        }
    }
}
