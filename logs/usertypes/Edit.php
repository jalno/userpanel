<?php
namespace packages\userpanel\logs\usertypes;

use packages\base\{View};
use packages\userpanel\{logs\Panel, Logs};

class Edit extends Logs {

	use UsertypeTrait;

	public function getColor():string{
		return "circle-success";
	}

	public function getIcon():string{
		return "fa fa-plus";
	}

	public function buildFrontend(View $view) {

		$view->addBodyClass("usertypes-edit-logs");

		$parameters = $this->log->parameters;

		$oldData = $parameters["old"] ?? [];
		$newData = $parameters["new"] ?? [];

		if ($oldData) {

			$html = $this->getHTML($oldData);

			if ($html) {

				$panel = new Panel("userpanel.usertypes.old");
				$panel->icon = "fa fa-trash";
				$panel->size = 6;
				$panel->title = t("userpanel.usertypes.logs.old_data");
	
				$panel->setHTML($html);
				$this->addPanel($panel);

			}
		}
		if ($newData) {
			
			$html = $this->getHTML($newData);

			if ($html) {

				$panel = new Panel("userpanel.usertypes.add");
				$panel->icon = "fa fa-plus";
				$panel->size = 6;
				$panel->title = t("userpanel.usertypes.logs.add_data");
	
				$panel->setHTML($html);
				$this->addPanel($panel);

			}
		}
	}
}
