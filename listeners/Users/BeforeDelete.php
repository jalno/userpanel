<?php
namespace packages\userpanel\listeners\users;

use packages\base\{View\Error};
use packages\userpanel\{Authorization, events as UserpanelEvents, Log as UserpanelLog};
use function packages\userpanel\url;

class BeforeDelete {
	public function check(UserpanelEvents\Users\BeforeDelete $event): void {
		$this->checkLogs($event);
	}
	private function checkLogs(UserpanelEvents\Users\BeforeDelete $event): void {
		$user = $event->getUser();
		$hasLogs = (new UserpanelLog)->where("user", $user->id)->has();
		if (!$hasLogs) {
			return;
		}

		$message = t("error.packages.userpanel.error.logs.user.delete_user_warn.message");
		$error = new Error("packages.userpanel.error.logs.user.delete_user_warn");
		$error->setType(Error::WARNING);
		if (Authorization::is_accessed("logs_search")) {
			$message .= "<br> " . t("packages.userpanel.error.logs.user.delete_user_warn.view_logs") . " ";
			$error->setData(array(
				array(
					"txt" => '<i class="fa fa-search"></i> ' . t("packages.userpanel.error.logs.user.delete_user_warn.view_logs_btn"),
					"type" => "btn-warning",
					"link" => url("logs/search", array(
						"user" => $user->id,
					)),
				),
			), "btns");
		} else {
			$message .= "<br> " . t("packages.userpanel.error.logs.user.delete_user_warn.view_logs.tell_someone");
		}
		$error->setMessage($message);

		$event->addError($error);
	}

}
