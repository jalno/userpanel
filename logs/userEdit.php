<?php
namespace packages\userpanel\logs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{logs\panel, logs};
class userEdit extends logs{
	public function getColor():string{
		return "circle-teal";
	}
	public function getIcon():string{
		return "fa fa-edit";
	}
	public function buildFrontend(view $view){
		$parameters = $this->log->parameters;
		$oldData = $parameters['oldData'];
		$newData = $parameters['newData'];

		$oldvisibilities = [];
		if(isset($oldData['visibilities'])){
			$oldvisibilities = $oldData['visibilities'];
			unset($oldData['visibilities']);
		}
		if($oldData){
			$panel = new panel('userpanel.user.logs.register');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('userpanel.user.logs.userEdit');
			$html = '';
			foreach($oldData as $field => $val){
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("register.user.{$field}").': </label>';
				$html .= '<div class="col-xs-8'.(!in_array($field, ['name', 'lastname']) ? " ltr" : "").'">'.$val.'</div>';
				$html .= "</div>";
			}
			$panel->setHTML($html);
			$this->addPanel($panel);
		}
		if((isset($newData['visibilities']) and $newData['visibilities']) or $oldvisibilities){
			$panel = new panel('userpanel.user.logs.register');
			$panel->icon = 'fa fa-external-link-square';
			$panel->size = 6;
			$panel->title = translator::trans('userpanel.user.logs.userEdit');
			$html = '';
			foreach($oldvisibilities as $field){
				$html .= '<div class="form-group">';
				$html .= '<label class="col-xs-4 control-label">'.translator::trans("userpnale.logs.userEdit.visibility_{$field}").': </label>';
				$html .= '<div class="col-xs-8'.(!in_array($field, ['name', 'lastname']) ? " ltr" : "").'">خصوصی شد</div>';
				$html .= "</div>";
			}
			if(isset($newData['visibilities'])){
				foreach($newData['visibilities'] as $field){
					$html .= '<div class="form-group">';
					$html .= '<label class="col-xs-4 control-label">'.translator::trans("userpnale.logs.userEdit.visibility_{$field}").': </label>';
					$html .= '<div class="col-xs-8'.(!in_array($field, ['name', 'lastname']) ? " ltr" : "").'">عمومی شد</div>';
					$html .= "</div>";
				}
			}

			$panel->setHTML($html);
			$this->addPanel($panel);
		}
	}
}
