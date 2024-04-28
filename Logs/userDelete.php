<?php

namespace packages\userpanel\Logs;

use packages\base\Translator;
use packages\base\View;
use packages\userpanel\Logs;

class UserDelete extends Logs
{
    public function getColor(): string
    {
        return 'circle-bricky';
    }

    public function getIcon(): string
    {
        return 'fa fa-trash';
    }

    public function buildFrontend(View $view)
    {
        $parameters = $this->log->parameters;
        $user = $parameters['user'];
        $panel = new Panel('userpanel.user.logs.register');
        $panel->icon = 'fa fa-external-link-square';
        $panel->size = 6;
        $panel->title = Translator::trans('userpanel.user.logs.userDelete');
        $html = '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.name').': </label>';
        $html .= '<div class="col-xs-8">'.$user->name.'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.lastname').': </label>';
        $html .= '<div class="col-xs-8">'.$user->lastname.'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.phone').': </label>';
        $html .= '<div class="col-xs-8 ltr">'.$user->phone.'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.cellphone').': </label>';
        $html .= '<div class="col-xs-8 ltr">'.$user->cellphone.'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.email').': </label>';
        $html .= '<div class="col-xs-8 ltr">'.$user->email.'</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.Translator::trans('register.user.address').': </label>';
        $html .= '<div class="col-xs-8">'.$user->address.'</div>';
        $html .= '</div>';
        $panel->setHTML($html);
        $this->addPanel($panel);
    }
}
