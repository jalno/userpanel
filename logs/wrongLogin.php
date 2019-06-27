<?php
namespace packages\userpanel\logs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{date, logs\box, logs\panel, logs};
class wrongLogin extends logs{
	public static function isActivity() {
		return false;
	}
	public function getColor():string{
		return "circle-bricky";
	}
	public function getIcon():string{
		return "fa fa-user-times";
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;

		$panel = new panel('userpanel.user.wronglogin');
		$panel->icon = 'fa fa-external-link-square';
		$panel->size = 6;
		$panel->title = translator::trans('userpanel.user.logs.wrongLogin');
		$html = '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("userpanel.user.logs.wrongLogin.wrongPassword").': </label>';
		$html .= '<div class="col-xs-8 ltr">'.$parameters['wrongpaswd'].'</div>';
		$html .= '</div>';
		$panel->setHTML($html);
		$this->addPanel($panel);
	}
}
