<?php

namespace themes\clipone\Views\Users;

use packages\base\Http;
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Authorization;
use packages\userpanel\User;
use packages\userpanel\Views\Users\ListView as usersListView;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class ListView extends usersListView
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;
    protected $types = [];

    public function __beforeLoad()
    {
        $this->setTitle(t('users'));
        $this->setButtons();
        Navigation::active('users/list');
        $this->types = Authorization::childrenTypes();
    }

    public function setButtons()
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

    public static function onSourceLoad()
    {
        parent::onSourceLoad();
        if (parent::$navigation) {
            $item = new MenuItem('users');
            $item->setTitle(t('users'));
            $item->setURL(userpanel\url('users'));
            $item->setIcon('clip-users');
            Navigation::addItem($item);
        }
    }

    public function getTypesForSelect()
    {
        $options = [['title' => '', 'value' => '']];
        foreach ($this->getUserTypes() as $type) {
            $options[] = [
                'title' => $type->title,
                'value' => $type->id,
            ];
        }

        return $options;
    }

    public function getStatusForSelect()
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

    public function getComparisonsForSelect()
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

    protected function getFormData(): array
    {
        return HTTP::$request['get'] ?? [];
    }
}
