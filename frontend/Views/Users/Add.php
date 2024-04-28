<?php

namespace themes\clipone\Views\Users;

use packages\base\{Options};
use packages\userpanel\Country;
use packages\userpanel\User;
use packages\userpanel\Views\Users\Add as UsersAddView;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\CountryCodeToReigonCodeTrait;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

use function packages\userpanel\url;

class Add extends UsersAddView
{
    use CountryCodeToReigonCodeTrait;
    use ViewTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('user.add'));
        $this->addBodyClass('users');
        $this->addBodyClass('users_add');
        $this->setNavigation();
        $this->initFormData();
        $this->prepairDynamicData();
    }

    protected function getCountriesForSelect(): array
    {
        return array_map(function ($country) {
            return [
                'title' => $country->name,
                'value' => $country->id,
            ];
        }, $this->getCountries());
    }

    protected function getTypesForSelect(): array
    {
        return array_map(function ($type) {
            return [
                'title' => $type->title,
                'value' => $type->id,
            ];
        }, $this->getTypes());
    }

    protected function getInputGroupArrayFor(string $field): array
    {
        $inputGroup = [
            'left' => [],
            'right' => [],
        ];
        $privacy = $this->getFieldPrivacyGroupBtn($field);
        if ($privacy) {
            $inputGroup['last'][] = $privacy;
        }
        if (in_array($field, ['phone', 'cellphone'])) {
            $inputGroup['first'][] = [
                'type' => 'select',
                'name' => "{$field}[code]",
                'options' => [],
            ];
        }

        return $inputGroup;
    }

    protected function getFieldPrivacyGroupBtn(string $field): ?array
    {
        $privacy = true;

        return $this->canEditPrivacy ? [
            'type' => 'button',
            'icon' => $privacy ? 'fa fa-eye' : 'fa fa-eye-slash',
            'text' => t('user.edit.privacy.'.($privacy ? 'public' : 'private')),
            'class' => ['btn', 'btn-default'],
            'dropdown' => [
                [
                    'icon' => 'fa fa-eye',
                    'link' => '#',
                    'class' => ['changevisibity'],
                    'data' => [
                        'field' => $field,
                        'visibility' => 'public',
                    ],
                    'title' => t('user.edit.privacy.public'),
                ],
                [
                    'icon' => 'fa fa-eye-slash',
                    'link' => '#',
                    'class' => ['changevisibity'],
                    'data' => [
                        'field' => $field,
                        'visibility' => 'private',
                    ],
                    'title' => t('user.edit.privacy.private'),
                ],
            ],
        ] : null;
    }

    private function setNavigation(): void
    {
        $item = new MenuItem('users');
        $item->setTitle(t('users'));
        $item->setURL(url('users'));
        $item->setIcon('clip-users');
        Breadcrumb::addItem($item);

        $item = new MenuItem('add');
        $item->setTitle(t('user.add'));
        $item->setIcon('clip-user-plus');
        Breadcrumb::addItem($item);

        Navigation::active('users/list');
    }

    private function initFormData(): void
    {
        if (!$this->getDataForm('country')) {
            $country = Country::getDefaultCountry();
            $this->setDataForm($country->id, 'country');
        }
        if (!$this->getDataForm('type')) {
            $options = Options::get('packages.userpanel.register');
            if (isset($options['type'])) {
                $this->setDataForm($options['type'], 'type');
            }
        }
        if (!$this->getDataForm('credit')) {
            $this->setDataForm(0, 'credit');
        }
        if (!$this->getDataForm('status')) {
            $this->setDataForm(User::active, 'status');
        }
    }

    private function prepairDynamicData()
    {
        $dd = $this->dynamicData();
        $dd->setData('countriesCode', $this->generateCountiesArray());
        $dd->setData('defaultCountryCode', $this->getDefaultCountryCode());
    }
}
