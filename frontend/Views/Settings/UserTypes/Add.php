<?php

namespace themes\clipone\Views\Settings\UserTypes;

use packages\base\Options;
use packages\userpanel\Views\Settings\UserTypes\Add as UserTypeEdit;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\UserTypesTrait;
use themes\clipone\ViewTrait;

class Add extends UserTypeEdit
{
    use ViewTrait;
    use FormTrait;
    use UserTypesTrait;

    public function __beforeLoad()
    {
        $this->setTitle([
            t('settings'),
            t('usertype.add'),
        ]);
        Navigation::active('settings/usertypes');
        $this->addBodyClass('usertypes');
        $this->addBodyClass('add-usertype');
        $this->dynamicData()->setData('usertypePermissions', $this->buildPermissionsArray());
    }

    public function export(): array
    {
        $permissions = $this->buildPermissionsArray(true);

        return [
            'data' => [
                'count' => count($permissions),
                'permissions' => $permissions,
            ],
        ];
    }

    protected function buildPermissionsArray(bool $withTranslate = false): array
    {
        $disabledPermissions = Options::get('packages.userpanel.disabledpermisions');
        $disabledPermissions = ($disabledPermissions and is_array($disabledPermissions)) ? $disabledPermissions : [];
        $permissions = [];
        foreach ($this->getPermissions() as $permission) {
            if (in_array($permission, $disabledPermissions)) {
                continue;
            }
            $item = [
                'key' => $permission,
            ];
            if ($withTranslate) {
                $item['title'] = $this->translatePermission($permission);
            }
            $permissions[] = $item;
        }

        return $permissions;
    }

    protected function translatePermission($permission)
    {
        $trans = t('usertype.permissions.'.$permission);

        return $trans ? $trans : $permission;
    }
}
