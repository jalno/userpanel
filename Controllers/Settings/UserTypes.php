<?php

namespace packages\userpanel\Controllers\Settings;

use packages\base\DB;
use packages\base\HTTP;
use packages\base\NotFound;
use packages\base\Response;
use packages\base\View;
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\AuthorizationException;
use packages\userpanel\Controller;
use packages\userpanel\Log;
use packages\userpanel\Logs;
use packages\userpanel\User;
use packages\userpanel\UserType;
use packages\userpanel\UserType\Permission;
use packages\userpanel\UserType\Permissions;
use packages\userpanel\UserType\Priority;
use packages\userpanel\Validators;
use packages\userpanel\Views;

use function packages\userpanel\url;

class UserTypes extends Controller
{
    private static function getUserType($data): UserType
    {
        $types = Authorization::childrenTypes();
        if (!$types or !in_array($data['type'], $types)) {
            throw new NotFound();
        }
        $model = (new UserType())
            ->with('permissions')
            ->with('children')
            ->where('id', $data['type'])
            ->getOne();
        if (!$model) {
            throw new NotFound();
        }

        return $model;
    }

    /**
     * @var bool require authentication
     */
    protected $authentication = true;

    public function listTypes()
    {
        Authorization::haveOrFail('settings_usertypes_list');
        $view = View::byName(Views\Settings\UserTypes\ListView::class);
        $this->response->setView($view);

        $types = Authorization::childrenTypes();

        $inputs = $this->checkinputs([
            'id' => [
                'type' => 'number',
                'optional' => true,
                'values' => $types,
            ],
            'title' => [
                'type' => 'string',
                'optional' => true,
            ],
            'word' => [
                'type' => 'string',
                'optional' => true,
            ],
            'comparison' => [
                'values' => ['equals', 'startswith', 'contains'],
                'default' => 'contains',
                'optional' => true,
            ],
        ]);

        $model = new UserType();
        if ($types) {
            $model->where('id', $types, 'in');
        } else {
            $model->where('id', null, 'is');
        }
        foreach (['id', 'title'] as $item) {
            if (isset($inputs[$item])) {
                $comparison = $inputs['comparison'];
                if (in_array($item, ['id'])) {
                    $comparison = 'equals';
                }
                $model->where($item, $inputs[$item], $comparison);
            }
        }

        if (isset($inputs['word']) and $inputs['word']) {
            $parenthesis = new DB\Parenthesis();
            foreach (['title'] as $item) {
                if (!isset($inputs[$item]) or !$inputs[$item]) {
                    $parenthesis->where($item, $inputs['word'], $inputs['comparison'], 'OR');
                }
            }
            $model->where($parenthesis);
        }

        $model->pageLimit = $this->items_per_page;
        $models = $model->paginate($this->page);
        $this->total_pages = $model->totalPages;
        $view->setDataList($models);
        $view->setPaginate($this->page, $model->totalCount, $this->items_per_page);

        $this->response->setStatus(true);

        return $this->response;
    }

    public function view($data): Response
    {
        if (!Authorization::is_accessed('users_edit_permissions') and !Authorization::is_accessed('settings_usertypes_edit')) {
            throw new AuthorizationException('userpanel_usertypes');
        }
        $usertype = self::getUserType($data);
        $this->response->setData($usertype->toArray(), 'usertype');
        $me = Authentication::getUser();
        $allPermissions = Permissions::existentForUser($me);
        $this->response->setData($allPermissions, 'all_permissions');
        $this->response->setStatus(true);

        return $this->response;
    }

    public function add()
    {
        Authorization::haveOrFail('settings_usertypes_add');
        $view = View::byName(Views\Settings\UserTypes\Add::class);
        $this->response->setView($view);

        $me = Authentication::getUser();
        $allPermissions = Permissions::existentForUser($me);
        $childrenTypes = Authorization::childrenTypes();

        // pass data to view
        $view->setPermissions($allPermissions);
        $view->setChildrenTypes($childrenTypes ? (new UserType())->where('id', $childrenTypes, 'in')->get() : []);

        if (HTTP::is_post()) {
            $this->response->setStatus(false);
            $inputs = $this->checkinputs([
                'title' => [
                    'type' => 'string',
                    'optional' => true,
                ],
                'permissions' => [
                    'type' => Validators\UserTypePermissionsValidator::class,
                    'values' => $allPermissions,
                ],
                'priorities' => [
                    'type' => Validators\UserTypePrioritiesValidator::class,
                    'optional' => true,
                    'default' => [],
                    'values' => $childrenTypes,
                ],
            ]);

            $usertype = new UserType();
            $usertype->title = $inputs['title'];
            $usertype->save();

            $parameters = [
                'new' => [
                    'usertype' => $usertype->toArray(),
                    'priorities' => $inputs['priorities'],
                    'permissions' => $inputs['permissions'],
                ],
            ];

            $parentTypes = array_merge($me->parentTypes(), [$me->type->id]);

            foreach (array_unique($parentTypes) as $type) {
                $priority = new Priority();
                $priority->parent = $type;
                $priority->child = $usertype->id;
                $priority->save();
            }

            foreach ($inputs['permissions'] as $name) {
                $permission = new Permission();
                $permission->type = $usertype->id;
                $permission->name = $name;
                $permission->save();
            }

            $priorities = [];
            foreach ($inputs['priorities'] as $item) {
                $priority = new Priority();
                $priority->parent = $usertype->id;
                $priority->child = $item;
                $priority->save();
            }

            $log = new Log();
            $log->title = t('packages.userpanel.logs.usertypes_add', ['id' => $usertype->id, 'title' => $usertype->title]);
            $log->type = Logs\UserTypes\Add::class;
            $log->user = $me->id;
            $log->parameters = $parameters;
            $log->save();

            $this->response->setStatus(true);
            $this->response->Go(url('settings/usertypes/edit/'.$usertype->id));
        } else {
            $this->response->setStatus(true);
        }

        return $this->response;
    }

    public function edit($data)
    {
        Authorization::haveOrFail('settings_usertypes_edit');
        $usertype = self::getUserType($data);

        $view = View::byName(Views\Settings\UserTypes\Edit::class);
        $this->response->setView($view);

        $me = Authentication::getUser();
        $allPermissions = Permissions::existentForUser($me);

        $childrenTypes = Authorization::childrenTypes();
        $usertypePermissions = array_column($usertype->toArray()['permissions'], 'name');
        $usertypePriorities = array_column($usertype->toArray()['children'], 'child');

        $view->setDataForm($usertype->title, 'title');
        $view->setUserType($usertype);
        $view->setPermissions($allPermissions);
        $view->setChildrenTypes((new UserType())->where('id', $childrenTypes, 'in')->get());

        if (HTTP::is_post()) {
            $inputs = $this->checkinputs([
                'title' => [
                    'type' => 'string',
                    'optional' => true,
                ],
                'permissions' => [
                    'type' => Validators\UserTypePermissionsValidator::class,
                    'values' => $allPermissions,
                ],
                'priorities' => [
                    'type' => Validators\UserTypePrioritiesValidator::class,
                    'optional' => true,
                    'default' => [],
                    'values' => $childrenTypes,
                ],
            ]);

            $parameters = [
                'old' => [
                    'usertype' => [],
                    'priorities' => [],
                    'permissions' => [],
                ],
                'new' => [
                    'usertype' => [],
                    'priorities' => [],
                    'permissions' => [],
                ],
            ];

            if ($inputs['title'] != $usertype->title) {
                $parameters['old']['usertype']['title'] = $usertype->title;
                $usertype->title = $inputs['title'];
                $parameters['new']['usertype']['title'] = $usertype->title;
            }

            $oldPermissions = array_column($usertype->permissions, 'name');

            $shouldBePersistentPermissions = array_diff($oldPermissions, $allPermissions);

            $permissions = $inputs['permissions'];

            if ($shouldBePersistentPermissions) {
                $permissions = array_merge($permissions, $shouldBePersistentPermissions);
                $permissions = array_values(array_unique($permissions));
            }

            $addedPermissions = array_diff($permissions, $oldPermissions);
            $removedPermissions = array_diff($oldPermissions, $permissions);

            if ($addedPermissions) {
                $parameters['new']['permissions'] = array_values($addedPermissions);

                foreach ($addedPermissions as $name) {
                    $permission = new Permission();
                    $permission->type = $usertype->id;
                    $permission->name = $name;
                    $permission->save();
                }
            }
            if ($removedPermissions) {
                $parameters['old']['permissions'] = array_values($removedPermissions);

                DB::where('name', array_values($removedPermissions), 'IN')
                    ->where('type', $usertype->id)
                    ->delete('userpanel_usertypes_permissions');
            }

            $prioritiesDelete = array_diff($usertypePriorities, $inputs['priorities']);

            if (!empty($prioritiesDelete)) {
                $parameters['old']['priorities'] = array_values($prioritiesDelete);

                DB::where('child', array_values($prioritiesDelete), 'IN')
                    ->where('parent', $usertype->id)
                    ->delete('userpanel_usertypes_priorities');
            }

            $newPriorities = array_diff($inputs['priorities'], $usertypePriorities);

            if ($newPriorities) {
                $parameters['new']['priorities'] = $newPriorities;

                foreach ($newPriorities as $item) {
                    $priority = new Priority();
                    $priority->parent = $usertype->id;
                    $priority->child = $item;
                    $priority->save();
                }
            }

            $usertype->save();

            $log = new Log();
            $log->title = t('packages.userpanel.logs.usertypes_edit', ['id' => $usertype->id, 'title' => $usertype->title]);
            $log->type = Logs\UserTypes\Edit::class;
            $log->user = $me->id;
            $log->parameters = $parameters;
            $log->save();

            $this->response->setStatus(true);
            $this->response->Go(url('settings/usertypes/edit/'.$usertype->id));
        } else {
            $this->response->setStatus(true);
        }

        return $this->response;
    }

    public function delete($data)
    {
        Authorization::haveOrFail('settings_usertypes_delete');
        $usertype = self::getUserType($data);

        $view = View::byName(Views\Settings\UserTypes\Delete::class);
        $this->response->setView($view);
        $view->setUserType($usertype);

        $this->response->setStatus(false);

        if (HTTP::is_post()) {
            if ((new User())->where('type', $usertype->id)->has()) {
                throw new View\Error('usertype.in_use');
            }

            $parameters = [
                'old' => [
                    'usertype' => $usertype->toArray(),
                    'permissions' => array_column($usertype->permissions, 'name'),
                    'priorities' => array_column($usertype->children, 'child'),
                ],
            ];

            $title = t('packages.userpanel.logs.usertypes_delete', ['id' => $usertype->id, 'title' => $usertype->title]);

            $usertype->delete();

            $log = new Log();
            $log->title = $title;
            $log->type = Logs\UserTypes\Delete::class;
            $log->user = Authentication::getID();
            $log->parameters = $parameters;
            $log->save();

            $this->response->setStatus(true);
            $this->response->Go(url('settings/usertypes'));
        } else {
            $this->response->setStatus(true);
        }

        return $this->response;
    }
}
