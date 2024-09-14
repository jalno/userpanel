<?php

namespace packages\userpanel\Logs;

use packages\base\Translator;
use packages\base\View;
use packages\userpanel\Logs;

class WrongLogin extends Logs
{
    public static function isActivity()
    {
        return false;
    }

    public function getColor(): string
    {
        return 'circle-bricky';
    }

    public function getIcon(): string
    {
        return 'fa fa-user-times';
    }

    public function buildFrontend(View $view)
    {
        $parameters = $this->log->parameters;

        $panel = new Panel('userpanel.user.wronglogin');
        $panel->icon = 'fa fa-external-link-square';
        $panel->size = 6;
        $panel->title = t('userpanel.user.logs.wrongLogin');
        $html = '<div class="form-group">';
        $html .= '<label class="col-xs-4 control-label">'.t('userpanel.user.logs.wrongLogin.wrongPassword').': </label>';
        $html .= '<div class="col-xs-8 ltr">'.$parameters['wrongpaswd'].'</div>';
        $html .= '</div>';
        $panel->setHTML($html);
        $this->addPanel($panel);
    }
}
