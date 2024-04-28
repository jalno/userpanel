<?php

namespace packages\userpanel\Logs;

use packages\base\View;
use packages\userpanel\Logs;
use packages\userpanel\UserType;

class UserEdit extends Logs
{
    public function getColor(): string
    {
        return 'circle-teal';
    }

    public function getIcon(): string
    {
        return 'fa fa-edit';
    }

    public function buildFrontend(View $view)
    {
        $view->addBodyClass('users-edit-logs');
        $parameters = $this->log->parameters;
        $oldData = $parameters['oldData'];
        $newData = $parameters['newData'];
        $isLtr = false;
        if ($oldData) {
            $panel = new Panel('userpanel.user.logs.userEdit.oldData');
            $panel->icon = 'fa fa-trash';
            $panel->size = 6;
            $panel->title = t('userpanel.user.logs.userEdit.old.data');
            $html = '';
            if (isset($oldData['avatar'])) {
                unset($oldData['avatar']);
            }
            foreach ($oldData as $field => $val) {
                if ('visibilities' == $field) {
                    continue;
                }
                if (is_array($val) and isset($val['title'], $val['value'])) {
                    $title = $val['title'];
                    $value = $val['value'];
                    $isLtr = !is_string($value);
                } elseif ('type' == $field) {
                    $title = t("log.user.{$field}");
                    $types = new UserType();
                    $types->where('id', $val);
                    $types = $types->getOne();
                    if (null != $types) {
                        $value = $types->title;
                    } else {
                        $value = $val;
                    }
                    $isLtr = '';
                } elseif ('has_custom_permissions' == $field) {
                    $title = t('packages.userpanel.logs.userEdit.has_custom_permissions');
                    $value = true == $val ? t('packages.userpanel.logs.userEdit.yes') : t('packages.userpanel.logs.userEdit.no');
                } else {
                    $title = t("log.user.{$field}");
                    $value = $val;
                    $isLtr = !in_array($field, ['name', 'lastname']);
                }
                $html .= '<div class="form-group">';
                $html .= '<label class="col-sm-6 col-xs-12 control-label">'.$title.': </label>';
                $html .= '<div class="col-sm-6 col-xs-12'.($isLtr ? ' ltr' : '').'">'.$value.'</div>';
                $html .= '</div>';
            }
            if (isset($oldData['visibilities'])) {
                foreach ($oldData['visibilities'] as $field) {
                    $html .= '<div class="form-group">';
                    $html .= '<label class="col-sm-6 col-xs-12 control-label">'.t("userpnale.logs.userEdit.visibility_{$field}").': </label>';
                    $html .= '<div class="col-sm-6 col-xs-12">عمومی</div>';
                    $html .= '</div>';
                }
            }
            if (isset($newData['visibilities'])) {
                foreach ($newData['visibilities'] as $field) {
                    $html .= '<div class="form-group">';
                    $html .= '<label class="col-sm-6 col-xs-12 control-label">'.t("userpnale.logs.userEdit.visibility_{$field}").': </label>';
                    $html .= '<div class="col-sm-6 col-xs-12">خصوصی</div>';
                    $html .= '</div>';
                }
            }
            if ($html) {
                $panel->setHTML($html);
                $this->addPanel($panel);
            }
        }
        if ($newData) {
            $panel = new Panel('userpanel.user.logs.register');
            $panel->icon = 'fa fa-plus';
            $panel->size = 6;
            $panel->title = t('userpanel.user.logs.userEdit.new.data');
            $html = '';
            if (isset($newData['avatar'])) {
                $html .= '<div class="form-group">';
                $html .= '<label class="col-sm-6 col-xs-12 control-label">'.t('user.avatar').': </label>';
                $html .= '<div class="col-sm-6 col-xs-12">'.t('packages.userpanel.logs.userEdit.changed').'</div>';
                $html .= '</div>';
                unset($newData['avatar']);
            }
            foreach ($newData as $field => $val) {
                if ('visibilities' == $field) {
                    continue;
                }
                if (is_array($val) and isset($val['title'], $val['value'])) {
                    $title = $val['title'];
                    $value = $val['value'];
                    $isLtr = !is_string($value);
                } elseif ('type' == $field) {
                    $title = t("log.user.{$field}");
                    $types = new UserType();
                    $types->where('id', $val);
                    $types = $types->getOne();
                    if (null != $types) {
                        $value = $types->title;
                    } else {
                        $value = $val;
                    }
                    $isLtr = '';
                } elseif ('has_custom_permissions' == $field) {
                    $title = t('packages.userpanel.logs.userEdit.has_custom_permissions');
                    $value = true == $val ? t('packages.userpanel.logs.userEdit.yes') : t('packages.userpanel.logs.userEdit.no');
                } else {
                    $title = t("log.user.{$field}");
                    $value = $val;
                    $isLtr = !in_array($field, ['name', 'lastname']);
                }
                $html .= '<div class="form-group">';
                $html .= '<label class="col-sm-6 col-xs-12 control-label">'.$title.': </label>';
                $html .= '<div class="col-sm-6 col-xs-12'.($isLtr ? ' ltr' : '').'">'.$value.'</div>';
                $html .= '</div>';
            }
            if (isset($newData['visibilities'])) {
                foreach ($newData['visibilities'] as $field) {
                    $html .= '<div class="form-group">';
                    $html .= '<label class="col-sm-6 col-xs-12 control-label">'.t("userpnale.logs.userEdit.visibility_{$field}").': </label>';
                    $html .= '<div class="col-sm-6 col-xs-12">عمومی</div>';
                    $html .= '</div>';
                }
            }
            if (isset($oldData['visibilities'])) {
                foreach ($oldData['visibilities'] as $field) {
                    $html .= '<div class="form-group">';
                    $html .= '<label class="col-sm-6 col-xs-12 control-label">'.t("userpnale.logs.userEdit.visibility_{$field}").': </label>';
                    $html .= '<div class="col-sm-6 col-xs-12">خصوصی</div>';
                    $html .= '</div>';
                }
            }
            if ($html) {
                $panel->setHTML($html);
                $this->addPanel($panel);
            }
        }
        if (isset($parameters['permissions'])) {
            if (isset($parameters['permissions']['addedPermissions']) and $parameters['permissions']['addedPermissions']) {
                $panel = new Panel('userpanel.user.logs.permission.added_permissions');
                $panel->icon = 'fa fa-plus-circle';
                $panel->size = 6;
                $panel->title = t('packages.userpanel.logs.userEdit.permissions.added_permissions');
                $html = '<ul class="list-group">';
                foreach ($parameters['permissions']['addedPermissions'] as $permission) {
                    $html .= '<li class="list-group-item"><i class="fa fa-plus text-success"></i> '.t("usertype.permissions.{$permission}").'</li>';
                }
                $html .= '</ul>';
                $panel->setHTML($html);
                $this->addPanel($panel);
            }
            if (isset($parameters['permissions']['removedPermissions']) and $parameters['permissions']['removedPermissions']) {
                $panel = new Panel('userpanel.user.logs.permission.added_permissions');
                $panel->icon = 'fa fa-minus-circle';
                $panel->size = 6;
                $panel->title = t('packages.userpanel.logs.userEdit.permissions.removed_permissions');
                $html = '<ul class="list-group">';
                foreach ($parameters['permissions']['removedPermissions'] as $permission) {
                    $html .= '<li class="list-group-item"><i class="fa fa-minus text-danger"></i> '.t("usertype.permissions.{$permission}").'</li>';
                }
                $html .= '</ul>';
                $panel->setHTML($html);
                $this->addPanel($panel);
            }
        }
    }
}
