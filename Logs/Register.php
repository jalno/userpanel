<?php

namespace packages\userpanel\Logs;

use packages\base\Translator;
use packages\base\View;
use packages\userpanel\Logs;

class Register extends Logs
{
    public static function isActivity()
    {
        return false;
    }

    public function getColor(): string
    {
        return 'circle-green';
    }

    public function getIcon(): string
    {
        return 'fa fa-user-plus';
    }

    public function buildFrontend(View $view)
    {
        $parameters = $this->log->parameters;
        $inputs = $parameters['inputs'];

        $panel = new Panel('userpanel.user.logs.register');
        $panel->icon = 'fa fa-external-link-square';
        $panel->size = 6;
        $panel->title = Translator::trans('userpanel.user.logs.register');
        $html = '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.name').': </label>';
        $html .= '<div class="col-xs-8">'.$inputs['name'].'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.lastname').': </label>';
        $html .= '<div class="col-xs-8">'.$inputs['lastname'].'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.phone').': </label>';
        $html .= '<div class="col-xs-8 ltr">'.$inputs['phone'].'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.cellphone').': </label>';
        $html .= '<div class="col-xs-8 ltr">'.$inputs['cellphone'].'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.email').': </label>';
        $html .= '<div class="col-xs-8 ltr">'.$inputs['email'].'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.address').': </label>';
        $html .= '<div class="col-xs-8">'.$inputs['address'].'</div>';
        $html .= '</div>';
        $panel->setHTML($html);
        $this->addPanel($panel);
    }
}
