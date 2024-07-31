<?php

namespace themes\clipone\Views\Profile;

use packages\base\Frontend\Theme;
use packages\base\Packages;
use packages\base\Translator;
use packages\userpanel\Views\Profile\Edit as ProfileEditView;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\CountryCodeToReigonCodeTrait;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\TabTrait;
use themes\clipone\ViewTrait;

use function packages\userpanel\url;

class Edit extends ProfileEditView
{
    use CountryCodeToReigonCodeTrait;
    use ViewTrait;
    use FormTrait;
    use TabTrait;

    protected $user;

    public function __beforeLoad()
    {
        $this->user = $this->getData('user');
        $this->setTitle([
            t('profile.edit'),
        ]);

        $this->addBodyClass('profile');
        $this->addBodyClass('profile_edit');
        $this->setNavigation();
        $this->prepairDynamicData();
    }

    private function setNavigation()
    {
        $item = new MenuItem('profile');
        $item->setTitle(t('profile.view'));
        $item->setURL(url('profile/view'));
        $item->setIcon('fa fa-user');
        Breadcrumb::addItem($item);

        $editItem = new MenuItem('edit');
        $editItem->setTitle(t('profile.edit'));
        $editItem->setURL(url('profile/edit'));
        $editItem->setIcon('fa fa-edit');
        Breadcrumb::addItem($editItem);

        Navigation::active('dashboard');
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

    protected function getAvatarURL(): string
    {
        if ($this->getUserData('avatar')) {
            return Packages::package('userpanel')->url($this->getUserData('avatar'));
        } else {
            return Theme::url('assets/images/defaultavatar.jpg');
        }
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
                'disabled' => 'cellphone' == $field,
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

    private function prepairDynamicData(): void
    {
        $dd = $this->dynamicData();
        $dd->setData('countriesCode', $this->generateCountiesArray());
        $dd->setData('defaultCountryCode', $this->getDefaultCountryCode());
    }
}
