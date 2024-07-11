<?php

namespace themes\clipone\Views;

use packages\base\Options;
use packages\userpanel\Views\Login as LoginView;
use themes\clipone\ViewTrait;

class Login extends LoginView
{
    use CountryCodeToReigonCodeTrait;
    use ViewTrait;
    use FormTrait;

    protected $registerEnable = false;

    public function __beforeLoad()
    {
        $this->setTitle(t('login'));
        $registerOption = Options::load('packages.userpanel.register');
        $this->registerEnable = boolval($registerOption['enable']);
        $this->dynamicDataBuilder();
        $this->addBodyClass('login-page');
    }

    private function dynamicDataBuilder()
    {
        $dd = $this->dynamicData();
        $dd->setData('countriesCode', $this->generateCountiesArray());
        $dd->setData('defaultCountryCode', $this->getDefaultCountryCode());
    }
}
