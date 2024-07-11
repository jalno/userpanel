<?php

namespace themes\clipone\Views;

use packages\userpanel\Views\ResetPWD as ResetPWDView;
use themes\clipone\ViewTrait;

class ResetPWD extends ResetPWDView
{
    use CountryCodeToReigonCodeTrait;
    use ViewTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('userpanel.resetpwd'));
        $this->addBodyClass('login');
        $this->addBodyClass('resetpwd');
        $this->dynamicDataBuilder();
    }

    public function hasBlocked(): bool
    {
        foreach ($this->getErrors() as $error) {
            if ('userpanel.resetpwd.losingChance' == $error->getCode()) {
                return true;
            }
        }

        return false;
    }

    public function getChannelsOptions()
    {
        $channelOptions = [];
        $channelNames = $this->getData('channelsnames');
        foreach ($channelNames as $channelName) {
            $channelOptions[] = [
                'label' => t('notifications.channel.name.'.$channelName),
                'value' => $channelName,
            ];
        }

        return $channelOptions;
    }

    private function dynamicDataBuilder()
    {
        $dd = $this->dynamicData();
        $dd->setData('countriesCode', $this->generateCountiesArray());
        $dd->setData('defaultCountryCode', $this->getDefaultCountryCode());
    }
}
