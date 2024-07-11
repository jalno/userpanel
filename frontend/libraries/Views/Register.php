<?php

namespace themes\clipone\Views;

use packages\base\Options;
use packages\userpanel\Country;
use packages\userpanel\Register\RegisterField;
use packages\userpanel\Views\Register as RegisterView;
use themes\clipone\Users\RegisterTrait;
use themes\clipone\ViewTrait;

class Register extends RegisterView
{
    use ViewTrait;
    use FormTrait;
    use CountryCodeToReigonCodeTrait;
    use RegisterTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('register'));
        $this->addBodyClass('register');
        $this->initFormData();
        $this->dynamicDataBuilder();
    }

    public function getFields(): array
    {
        return array_map(
            function (array $item): array {
                $isCredientials = $item['is_crediential'] ?? false;
                $hasMoreFields = $item['has_more_non_credientials'] ?? false;
                if (RegisterField::PHONE == $item['value'] or $isCredientials or !$hasMoreFields) {
                    $item['classes'] = 'col-md-12';
                } else {
                    $item['classes'] = 'col-md-6';
                }
                unset($item['field']['label']);

                return $item;
            },
            $this->getRegisterFields()
        );
    }

    protected function getTOSUrl(): ?string
    {
        return Options::get('packages.userpanel.tos_url');
    }

    private function dynamicDataBuilder()
    {
        $dd = $this->dynamicData();

        $dd->setData('countriesCode', $this->generateCountiesArray());
        $dd->setData('defaultCountryCode', $this->getDefaultCountryCode());
    }

    private function initFormData(): void
    {
        if (!$this->getDataForm('country')) {
            $country = Country::getDefaultCountry();
            $this->setDataForm($country->id, 'country');
        }
    }
}
