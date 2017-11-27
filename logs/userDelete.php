<?php
namespace packages\userpanel\logs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{logs\panel, logs};
class userDelete extends logs{
	public function getColor():string{
		return "circle-bricky";
	}
	public function getIcon():string{
		return "fa fa-trash";
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$user = $parameters['user'];
		$panel = new panel('userpanel.user.logs.register');
		$panel->icon = 'fa fa-external-link-square';
		$panel->size = 6;
		$panel->title = translator::trans('userpanel.user.logs.userDelete');
		$html = '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.name").': </label>';
		$html .= '<div class="col-xs-8">'.$user->name.'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.lastname").': </label>';
		$html .= '<div class="col-xs-8">'.$user->lastname.'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.phone").': </label>';
		$html .= '<div class="col-xs-8 ltr">'.$user->phone.'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.cellphone").': </label>';
		$html .= '<div class="col-xs-8 ltr">'.$user->cellphone.'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.email").': </label>';
		$html .= '<div class="col-xs-8 ltr">'.$user->email.'</div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.address").': </label>';
		$html .= '<div class="col-xs-8">'.$user->address.'</div>';
		$html .= '</div>';
		$panel->setHTML($html);
		$this->addPanel($panel);
	}
}
