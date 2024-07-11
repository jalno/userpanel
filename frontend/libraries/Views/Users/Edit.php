<?php

namespace themes\clipone\Views\Users;

use packages\base\Frontend\Theme;
use packages\base\Options;
use packages\base\Packages;
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\UserType\Permissions;
use packages\userpanel\Views\Users\Edit as UsersEditView;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\CountryCodeToReigonCodeTrait;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\TabTrait;
use themes\clipone\ViewTrait;

class Edit extends UsersEditView
{
    use CountryCodeToReigonCodeTrait;
    use ViewTrait;
    use FormTrait;
    use TabTrait;

    protected $usertypes = [];
    private $user;

    public function __beforeLoad(): void
    {
        $this->user = $this->getData('user');
        $this->setTitle(t('profile.edit'));
        $this->addBodyClass('users');
        $this->addBodyClass('users_edit');
        $this->setNavigation();
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

    protected function getAvatarURL(): string
    {
        if ($this->user->avatar) {
            return Packages::package('userpanel')->url($this->user->avatar);
        } else {
            return Theme::url('assets/images/defaultavatar.jpg');
        }
    }

    protected function buildPermissionsArray(bool $withTranslate = false): array
    {
        $existentPermissions = Permissions::existentForUser(Authentication::getUser());
        $userPermissions = $this->user->getPermissions();

        return array_map(function (string $permission) use ($userPermissions, $withTranslate) {
            $item = [
                'key' => $permission,
                'value' => in_array($permission, $userPermissions),
            ];
            if ($withTranslate) {
                $item['title'] = t("usertype.permissions.{$permission}");
            }

            return $item;
        }, $existentPermissions);
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
        $privacy = $this->getDataForm('visibility_'.$field);

        return $this->canEditPrivacy ? [
            'type' => 'button',
            'icon' => $privacy ? 'fa fa-eye' : 'fa fa-eye-slash',
            'text' => Translator::trans('user.edit.privacy.'.($privacy ? 'public' : 'private')),
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
                    'title' => Translator::trans('user.edit.privacy.public'),
                ],
                [
                    'icon' => 'fa fa-eye-slash',
                    'link' => '#',
                    'class' => ['changevisibity'],
                    'data' => [
                        'field' => $field,
                        'visibility' => 'private',
                    ],
                    'title' => Translator::trans('user.edit.privacy.private'),
                ],
            ],
        ] : null;
    }

    protected function getUserCurrency(): string
    {
        if (Packages::package('financial')) {
            return \packages\financial\Currency::getDefault($this->user)->title;
        } else {
            return Options::get('packages.userpanel.users.credit.currency.title');
        }
    }

    private function setNavigation(): void
    {
        $item = new MenuItem('users');
        $item->setTitle(Translator::trans('users'));
        $item->setURL(userpanel\url('users'));
        $item->setIcon('clip-users');
        Breadcrumb::addItem($item);

        $item = new MenuItem('user');
        $item->setTitle($this->getData('user')->getFullName());
        $item->setURL(userpanel\url('users/view/'.$this->getDataForm('id')));
        $item->setIcon('clip-user');
        Breadcrumb::addItem($item);

        $item = new MenuItem('edit');
        $item->setTitle(Translator::trans('user.edit'));
        $item->setURL(userpanel\url('users/edit/'.$this->getDataForm('id')));
        $item->setIcon('clip-edit');
        Breadcrumb::addItem($item);

        Navigation::active('users/list');
    }

    protected function prepairDynamicData(): void
    {
        $dd = $this->dynamicData();
        $dd->setData('userPermissions', $this->buildPermissionsArray());

        $dd->setData('countriesCode', $this->generateCountiesArray());
        $dd->setData('defaultCountryCode', $this->getDefaultCountryCode());
    }
}
