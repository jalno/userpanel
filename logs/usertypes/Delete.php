<?php
namespace packages\userpanel\logs\usertypes;

use packages\base\{View};
use packages\userpanel\{logs\Panel, Logs};

class Delete extends Logs {

	use UsertypeTrait;

	public function getColor():string{
		return "circle-bricky";
	}

	public function getIcon():string{
		return "fa fa-trash";
	}

	public function buildFrontend(View $view) {

		$view->addBodyClass("usertypes-delete-logs");

		$parameters = $this->log->parameters;

		$oldData = $parameters["old"] ?? [];

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
	}
}
