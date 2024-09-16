<?php

namespace themes\clipone\Views\Logs;

use packages\userpanel\Authorization;
use packages\userpanel\User;
use packages\userpanel\Views\Logs\Search as LogsSearch;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class Search extends LogsSearch
{
    use ViewTrait;
    use FormTrait;
    use ListTrait;

    protected $multiuser;

    public function __beforeLoad()
    {
        $this->setTitle(t('users.logs'));
        $this->setButtons();
        Navigation::active('logs');
        $this->multiuser = (bool) Authorization::childrenTypes();
        $this->addBodyClass('users-logs');
        $this->setFormData();
    }

    private function setFormData()
    {
        $userID = $this->getDataForm('user');
        if ($userID) {
            $user = (new User())->byID($userID);
            if ($user) {
                $this->setDataForm($user->getFullName(), 'user_name');
            }
        }
    }

    public function setButtons()
    {
        $this->setButton('view', $this->canView, [
            'title' => t('logs.view'),
            'icon' => 'fa fa-credit-card',
            'classes' => ['btn', 'btn-xs', 'btn-green'],
        ]);
        $this->setButton('delete', $this->canDelete, [
            'title' => t('logs.delete'),
            'icon' => 'fa fa-times',
            'classes' => ['btn', 'btn-xs', 'btn-bricky'],
        ]);
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

    /**
     * Export logs to ajax or api requests.
     */
    public function export(): array
    {
        return [
            'data' => array_merge([
                'permissions' => [
                    'canView' => $this->canView,
                    'canDelete' => $this->canDelete,
                ],
                'items' => array_map(function ($log) {
                    $handler = $log->getHandler();

                    return [
                        'id' => $log->id,
                        'ip' => $log->ip,
                        'time' => $log->time,
                        'title' => $log->title,
                        'type' => $log->type,
                        'icon' => $handler->getIcon(),
                        'color' => $handler->getColor(),
                        'activity' => method_exists($handler, 'isActivity') ? $handler->isActivity() : true,
                    ];
                }, $this->getDataList()),
            ], $this->getCursorExportData()),
        ];
    }
}
