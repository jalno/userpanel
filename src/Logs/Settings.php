<?php

namespace packages\userpanel\Logs;

use packages\base\{View};
use packages\userpanel\Logs;

class Settings extends Logs
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
        $parameters = $this->log->parameters;
        $oldData = $parameters['oldData'];
        $newData = $parameters['newData'];
        if ($oldData) {
            $panel = new Panel('userpanel.settings.oldData');
            $panel->icon = 'fa fa-trash';
            $panel->size = 6;
            $panel->title = t('userpanel.old_data');
            $html = '';
            foreach ($oldData as $field => $val) {
                if (is_array($val) and isset($val['title'], $val['value'])) {
                    $title = $val['title'];
                    $value = $val['value'];
                    $isLtr = !is_string($value);
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
            $panel->setHTML($html);
            $this->addPanel($panel);
        }
        if ($newData) {
            $panel = new Panel('userpanel.settings.newData');
            $panel->icon = 'fa fa-plus';
            $panel->size = 6;
            $panel->title = t('userpanel.new_data');
            $html = '';
            foreach ($newData as $field => $val) {
                if (is_array($val) and isset($val['title'], $val['value'])) {
                    $title = $val['title'];
                    $value = $val['value'];
                    $isLtr = !is_string($value);
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
            $panel->setHTML($html);
            $this->addPanel($panel);
        }
    }
}
