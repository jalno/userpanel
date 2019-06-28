<?php
namespace packages\userpanel\logs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{logs\panel, logs};
class register extends logs{
	public static function isActivity() {
		return false;
	}
	public function getColor():string{
		return "circle-green";
	}
	public function getIcon():string{
		return "fa fa-user-plus";
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$inputs = $parameters['inputs'];

		$panel = new panel('userpanel.user.logs.register');
		$panel->icon = 'fa fa-external-link-square';
		$panel->size = 6;
		$panel->title = translator::trans('userpanel.user.logs.register');
		$html = '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.name").': </label>';
		$html .= '<div class="col-xs-8">'.$inputs['name'].'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.lastname").': </label>';
		$html .= '<div class="col-xs-8">'.$inputs['lastname'].'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.phone").': </label>';
		$html .= '<div class="col-xs-8 ltr">'.$inputs['phone'].'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.cellphone").': </label>';
		$html .= '<div class="col-xs-8 ltr">'.$inputs['cellphone'].'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.email").': </label>';
		$html .= '<div class="col-xs-8 ltr">'.$inputs['email'].'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.address").': </label>';
		$html .= '<div class="col-xs-8">'.$inputs['address'].'</div>';
		$html .= '</div>';
		$panel->setHTML($html);
		$this->addPanel($panel);
	}
}
