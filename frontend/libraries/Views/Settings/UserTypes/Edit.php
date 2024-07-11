<?php

namespace themes\clipone\Views\Settings\UserTypes;

use packages\base\Options;
use packages\base\View\Error;
use packages\userpanel\UserType;
use packages\userpanel\Views\Settings\UserTypes\Edit as UserTypeEdit;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\UserTypesTrait;
use themes\clipone\ViewTrait;

use function packages\userpanel\url;

class Edit extends UserTypeEdit
{
    use ViewTrait;
    use FormTrait;
    use UserTypesTrait;

    public function __beforeLoad()
    {
        $this->settitle([
            t('settings'),
            t('usertype.edit'),
        ]);
        Navigation::active('settings/usertypes');
        $this->addBodyClass('usertypes');
        $this->addBodyClass('edit-usertype');
        $this->dynamicData()->setData('usertypePermissions', $this->buildPermissionsArray());
        $this->addWarnings();
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
        $usertype = $this->getUserType();
        $permissions = [];
        foreach ($this->getPermissions() as $permission) {
            if (in_array($permission, $disabledPermissions)) {
                continue;
            }
            $item = [
                'key' => $permission,
                'value' => $usertype->hasPermission($permission),
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

    protected function hasPriority(UserType $priority)
    {
        foreach ($this->getUserType()->children as $child) {
            if ($priority->id == $child->data['child']) {
                return true;
            }
        }

        return false;
    }

    protected function addWarnings(): void
    {
        $hasCustomizedPermissionsUser = $this->hasCustomizedPermissionsUser();
        if ($hasCustomizedPermissionsUser) {
            $type = $this->getUserType();
            $error = new Error('packages.userpanel.usertypes.edit.has_custom_permissions_users');
            $error->setType(Error::WARNING);
            $error->setData([
                [
                    'txt' => '<i class="fa fa-search"></i> '.t('error.packages.userpanel.usertypes.edit.has_custom_permissions_users.view_users'),
                    'type' => 'btn-warning',
                    'link' => url('users', [
                        'type' => $type->id,
                        'has_custom_permissions' => true,
                    ]),
                ],
            ], 'btns');
            $this->addError($error);
        }
    }
}
