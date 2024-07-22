<?php

namespace themes\clipone\Views\Users;

use packages\base\Http;
use packages\userpanel;
use packages\userpanel\Authorization;
use packages\userpanel\User;
use packages\userpanel\Views\Users\Search as ParentView;
use themes\clipone\Navigation;
use themes\clipone\Views\CountryCodeToReigonCodeTrait;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class Search extends ParentView
{
    use CountryCodeToReigonCodeTrait;

    use ViewTrait;
    use ListTrait;
    use FormTrait;

    public static function onSourceLoad()
    {
        parent::onSourceLoad();
        if (!parent::$navigation) {
            return;
        }
        $item = new Navigation\MenuItem('users');
        $item->setTitle(t('users'));
        $item->setURL(userpanel\url('users'));
        $item->setIcon('clip-users');
        Navigation::addItem($item);
    }

    public bool $canAdd;
    protected $types = [];

    public function __construct()
    {
        parent::__construct();
        $this->canAdd = Authorization::is_accessed('users_add', 'userpanel');
    }

    public function __beforeLoad()
    {
        $this->setTitle(t('users'));
        $this->setButtons();
        $this->addBodyClass('userpanel');
        $this->addBodyClass('users-search');
        Navigation::active('users');
        $this->prepareDynamicData();
    }

    public function export()
    {
        return [
            'data' => [
                'items' => array_map(function (User $user) {
                    $array = $user->toArray(false);
                    $array['cellphone'] = $user->getCellphoneWithDialingCode();
                    $array['phone'] = $user->getPhoneWithDialingCode();

                    return $array;
                }, $this->dataList),
                'items_per_page' => (int) $this->itemsPage,
                'current_page' => (int) $this->currentPage,
                'total_items' => (int) $this->totalItems,
            ],
        ];
    }

    /**
     * @param string[]|string              $statuses
     * @param array<string|int>|string|int $values
     */
    public function isActiveStatusTab($statuses, $values): bool
    {
        static $otherTabsIsActive = false;

        if ($otherTabsIsActive) {
            return false;
        }

        $urlParameters = $this->getFormData();

        $statuses = (array) $statuses;
        $values = (array) $values;

        foreach ($statuses as $key => $status) {
            $value = ($values[$key] ?? $values[0]);
            if ((!$value and !isset($urlParameters[$status])) or (isset($urlParameters[$status]) and $urlParameters[$status] == $value)) {
                $otherTabsIsActive = true;

                return true;
            }
        }

        return false;
    }

    protected function setButtons(): void
    {
        $this->setButton('view', $this->canView, [
            'title' => t('user.profile'),
            'icon' => 'fa fa-credit-card',
            'classes' => ['btn', 'btn-xs', 'btn-green'],
        ]);
        $this->setButton('edit', $this->canEdit, [
            'title' => t('user.edit'),
            'icon' => 'fa fa-edit',
            'classes' => ['btn', 'btn-xs', 'btn-teal'],
        ]);
        $this->setButton('delete', $this->canDelete, [
            'title' => t('user.delete'),
            'icon' => 'fa fa-times',
            'classes' => ['btn', 'btn-xs', 'btn-bricky'],
        ]);
    }

    protected function getSelectedTypes(): array
    {
        $selected = $this->getDataForm('type-select');

        return ($selected and is_array($selected)) ? $selected : [];
    }

    protected function getTypesForSelect(): array
    {
        $options = [];
        foreach ($this->getUserTypes() as $type) {
            $options[] = [
                'title' => $type->title,
                'value' => $type->id,
            ];
        }

        return $options;
    }

    protected function getStatusForSelect(): array
    {
        return [
            [
                'title' => '',
                'value' => '',
            ],
            [
                'title' => t('user.status.active'),
                'value' => User::active,
            ],
            [
                'title' => t('user.status.suspend'),
                'value' => User::suspend,
            ],
            [
                'title' => t('user.status.deactive'),
                'value' => User::deactive,
            ],
        ];
    }

    protected function getComparisonsForSelect(): array
    {
        return [
            [
                'title' => t('search.comparison.contains'),
                'value' => 'contains',
            ],
            [
                'title' => t('search.comparison.equals'),
                'value' => 'equals',
            ],
            [
                'title' => t('search.comparison.startswith'),
                'value' => 'startswith',
            ],
        ];
    }

    protected function getCountriesForSelect(): array
    {
        $options = [['title' => '', 'value' => '']];
        foreach ($this->getCountries() as $country) {
            $options[] = [
                'title' => $country->name,
                'value' => $country->id,
            ];
        }

        return $options;
    }

    protected function getFormData(): array
    {
        return HTTP::$request['get'] ?? [];
    }

    private function prepareDynamicData(): void
    {
        $dd = $this->dynamicData();
        $dd->setData('countriesCode', $this->generateCountiesArray());
        $dd->setData('defaultCountryCode', $this->getDefaultCountryCode());
    }
}
