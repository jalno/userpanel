<?php

namespace packages\userpanel;

use packages\base\DB;
use packages\base\DB\DBObject;
use packages\base\IO;
use packages\base\Packages;
use packages\base\Utility;
use packages\base\Utility\Password;
use packages\userpanel\{User\Option,UserType,Country};

/**
 * @property int                  $id
 * @property string               $email
 * @property string               $name
 * @property string|null          $lastname
 * @property string               $cellphone
 * @property string               $password
 * @property UserType|int         $type
 * @property string|null          $phone
 * @property string|null          $city
 * @property Country|int          $country
 * @property int                  $zip
 * @property string|null          $address
 * @property string|null          $web
 * @property float                $credit
 * @property int                  $lastonline
 * @property string|null          $remember_token
 * @property int                  $registered_at
 * @property bool                 $has_custom_permissions
 * @property int                  $status
 * @property User\SocialNetwork[] $socialnetworks
 * @property User\Option[]        $options
 */
class User extends DBObject
{
    use ImageTrait;

    public const active = 1;
    public const deactive = 3;
    public const suspend = 2;
    public const onlineTimeout = 20;
    protected $dbTable = 'userpanel_users';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'email' => ['type' => 'text', 'required' => true, 'unique' => 'true'],
        'name' => ['type' => 'text', 'required' => true],
        'lastname' => ['type' => 'text'],
        'cellphone' => ['type' => 'text', 'required' => true, 'unique' => 'true'],
        'password' => ['type' => 'text', 'required' => true],
        'type' => ['type' => 'int', 'required' => true],
        'phone' => ['type' => 'text'],
        'city' => ['type' => 'text'],
        'country' => ['type' => 'text'],
        'zip' => ['type' => 'int'],
        'address' => ['type' => 'text'],
        'web' => ['type' => 'text'],
        'avatar' => ['type' => 'text'],
        'credit' => ['type' => 'double'],
        'lastonline' => ['type' => 'int'],
        'remember_token' => ['type' => 'text'],
        'registered_at' => ['type' => 'int', 'required' => true],
        'has_custom_permissions' => ['type' => 'int'],
        'status' => ['type' => 'int', 'required' => true],
    ];
    protected $relations = [
        'type' => ['hasOne', UserType::class, 'type'],
        'socialnetworks' => ['hasMany', User\SocialNetwork::class, 'user'],
        'options' => ['hasMany', User\Option::class, 'user'],
        'country' => ['hasOne', Country::class, 'country'],
    ];

    private $customPermissions;

    public function getFullName()
    {
        return $this->name.($this->lastname ? ' '.$this->lastname : '');
    }

    public function password_verify($password)
    {
        return Password::verify($password, $this->password);
    }

    public function password_hash($password)
    {
        $this->password = Password::hash($password);
    }

    public function setCustomPermissions(array $permissions): void
    {
        $typePermissions = array_column($this->type->permissions, 'name');
        /* if given permissions is not diffrent than type permissions, no need to use custom permissions! */
        if (empty(array_diff($typePermissions, $permissions)) and empty(array_diff($permissions, $typePermissions))) {
            $this->has_custom_permissions = false;
            $this->save();
            $this->customPermissions = [];
            DB::where('user_id', $this->id)->delete('userpanel_users_permissions');

            return;
        }
        $customPermissions = $this->has_custom_permissions ? $this->getCustomPermissions(false) : [];

        $removedPermissions = array_diff($customPermissions, $permissions);
        if ($removedPermissions) {
            $removedPermissions = array_values($removedPermissions);
            DB::where('user_id', $this->id)->where('permission', $removedPermissions, 'IN')->delete('userpanel_users_permissions');
        }

        $newPermissions = array_diff($permissions, $customPermissions);
        if ($newPermissions) {
            $userID = $this->id;
            DB::insertMulti('userpanel_users_permissions', array_map(function ($permission) use ($userID) {
                return [
                    'user_id' => $userID,
                    'permission' => $permission,
                ];
            }, array_values($newPermissions)));
        }
        $this->has_custom_permissions = true;
        $this->save();
        $this->customPermissions = $permissions;
    }

    public function getCustomPermissions(bool $useCache = true): array
    {
        if (null === $this->customPermissions or !$useCache) {
            $this->customPermissions = array_column(DB::where('user_id', $this->id)->get('userpanel_users_permissions', null, 'permission'),
                'permission'
            );
        }

        return $this->customPermissions;
    }

    public function can(string $permission): bool
    {
        if ($this->has_custom_permissions) {
            return in_array($permission, $this->getCustomPermissions());
        }

        return $this->type->hasPermission($permission);
    }

    public function childrenTypes()
    {
        $children = [];
        $types = $this->type->children;
        if ($types) {
            foreach ($types as $type) {
                $children[] = $type->child;
            }
        }

        return $children;
    }

    public function parentTypes(): array
    {
        $type = $this->type->id;
        DB::where('child', $type);

        return array_column(DB::get('userpanel_usertypes_priorities', null, ['parent']), 'parent');
    }

    public function isManager(): bool
    {
        $parents = $this->parentTypes();
        if (empty($parents)) {
            return true;
        }

        return 1 == count($parents) and $parents[0] == $this->type->id;
    }

    public function option($name, $value = null)
    {
        if ($value) {
            return $this->setOption($name, $value);
        } else {
            return $this->getOption($name);
        }
    }

    public function getOption($name)
    {
        foreach ($this->options as $option) {
            if ($option->name == $name) {
                return $option->value;
            }
        }

        return $this->type->option($name);
    }

    public function setOption($name, $value)
    {
        foreach ($this->options as $option) {
            if ($option->name == $name) {
                $option->value = $value;

                return $option->save();
            }
        }
        $option = new Option();
        $option->user = $this->id;
        $option->name = $name;
        $option->value = $value;

        return $option->save();
    }

    public function getVisibility($field)
    {
        $visibilities = $this->getOption('visibilities');

        return is_array($visibilities) and in_array($field, $visibilities);
    }

    public function setVisibility($field, $visible)
    {
        $visibilities = $this->getOption('visibilities');
        if (!is_array($visibilities)) {
            $visibilities = [];
        }
        if ($visible) {
            $visibilities[] = $field;
        } elseif (($key = array_search($field, $visibilities)) !== false) {
            unset($visibilities[$key]);
        }
        $visibilities = array_values(array_unique($visibilities));
        $this->setOption('visibilities', $visibilities);
    }

    public function save($data = null)
    {
        $oldavatar = null;
        if ($this->avatar) {
            $oldavatar = $this->avatar;
        }
        if ($id = parent::save($data)) {
            if ($oldavatar and $oldavatar != $this->avatar) {
                DB::where('avatar', $oldavatar);
                if (!DB::has($this->dbTable)) {
                    IO\Unlink(Packages::package('userpanel')->getFilePath($oldavatar));
                }
            }
        }

        return $id;
    }

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }
        if ($this->avatar) {
            DB::where('avatar', $this->avatar);
            if (!DB::has($this->dbTable)) {
                Packages::package('userpanel')->getFile($this->avatar)->delete();
            }
        }
    }

    public function toArray($recursive = false)
    {
        $password = $this->password;
        unset($this->data['password']);
        $remember_token = $this->remember_token;
        unset($this->data['remember_token']);
        $return = parent::toArray($recursive);
        $this->password = $password;
        $this->remember_token = $remember_token;

        return $return;
    }

    public function createRememberToken(): string
    {
        $letters = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $rememberToken = '';
        for ($i = 0; $i < 32; ++$i) {
            $rememberToken .= $letters[rand(0, 61)];
        }
        $this->remember_token = $rememberToken;
        $this->save();

        return $rememberToken;
    }

    public function online()
    {
        $this->lastonline = Date::time();

        return $this->save();
    }

    public function isOnline(): bool
    {
        return Date::time() - $this->lastonline < self::onlineTimeout;
    }

    public function getAvatar(int $width, int $height)
    {
        return $this->getImage($width, $height, 'avatar');
    }

    public function getPermissions(): array
    {
        if ($this->has_custom_permissions) {
            return $this->getCustomPermissions();
        }
        $typeID = $this->type->id;
        $permissions = (new UserType\Permission())->where('type', $typeID)->arrayBuilder()->get(null, 'name');

        return array_column($permissions, 'name');
    }

    public function preLoad(array $data): array
    {
        if (!isset($data['lastonline'])) {
            $data['lastonline'] = Date::time();
        }
        if (!isset($data['registered_at'])) {
            $data['registered_at'] = Date::time();
        }

        return $data;
    }

    public function getCellphoneWithDialingCode(): string
    {
        return utility\getTelephoneWithDialingCode($this->cellphone);
    }

    public function getPhoneWithDialingCode(): string
    {
        return $this->phone ? utility\getTelephoneWithDialingCode($this->phone) : '';
    }
}
