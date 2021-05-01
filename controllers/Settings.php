<?php
namespace packages\userpanel\controllers;

use packages\userpanel;
use themes\clipone\views;
use packages\base\{Date, Options, View, NotFound};
use packages\userpanel\{events\General\Settings as Event, events\General\Settings\Controller,
						events\General\Settings\Log, Authorization, Authentication, UserType, User, Logs};

class Settings extends userpanel\Controller implements Controller {
	
	protected $authentication = true;

	public function view() {
		Authorization::haveOrFail("settings_general-settings");
		$event = new Event();
		$event->trigger();
		if (!$event->get()) {
			throw new NotFound();
		}
		$view = View::byName(views\Settings::class);
		$view->setSettings($event->get());
		$this->response->setView($view);
		return $this->response;
	}
	public function update() {
		Authorization::haveOrFail("settings_general-settings");
		$event = new Event();
		$event->trigger();
		$settings = $event->get();
		if (!$settings) {
			throw new NotFound();
		}
		$view = View::byName(views\Settings::class);
		$view->setSettings($settings);
		$this->response->setView($view);
		$this->response->setStatus(false);
		$inputsRules = [];
		$logs = array();
		foreach ($settings as $setting) {
			if ($SRules = $setting->getInputs()) {
				$SRules = $inputsRules = array_merge($inputsRules, $SRules);
				$ginputs = $this->checkinputs($SRules);
				$logs = array_merge($logs, $setting->store($ginputs));
			}
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$this->response->setStatus(true);
		$inputs = array(
			"oldData" => array(),
			"newData" => array(),
		);
		foreach ($logs as $log) {
			$inputs["oldData"][$log->getName()] = array("title" => $log->getTitle(), "value" => $log->getOldValue());
			$inputs["newData"][$log->getName()] = array("title" => $log->getTitle(), "value" => $log->getNewValue());
		}
		if ($logs) {
			$log = new userpanel\Log();
			$log->title = t("log.settings.general-settings.update");
			$log->type = Logs\Settings::class;
			$log->user = Authentication::getUser();
			$log->parameters = $inputs;
			$log->save();
		}
		return $this->response;
	}
	public function store(array $inputs): array {
		$logs = array();
		$options = Options::get("packages.userpanel.register");
		if (isset($inputs["userpanel_register_enabled"])) {
			if (!isset($options["enable"]) or $options["enable"] != $inputs["userpanel_register_enabled"]) {
				$logs[] = new Log('userpanel_register_enabled', $options["enable"] ? t("active") : t("deactive"), $inputs["userpanel_register_enabled"] ? t("active") : t("deactive"), t('setting.userpanel.register.enabled'));
				$options["enable"] = $inputs["userpanel_register_enabled"];
			}
		}
		if (isset($inputs["userpanel_register_type"])) {
			if (!isset($options["type"]) or $options["type"] != $inputs["userpanel_register_type"]->id) {
				$type = null;
				if (isset($options["type"])) {
					$type = (new UserType)->byId($options["type"]);
				}
				$oldValue = $options["type"] ?? "-";
				if ($type) {
					$oldValue = $type->title;
				}
				$logs[] = new Log('userpanel_register_type', $oldValue, $inputs["userpanel_register_type"]->title, t('settings.userpanel.register.usertype'));
				$options["type"] = $inputs["userpanel_register_type"]->id;
			}
		}
		if (isset($inputs["userpanel_register_status"])) {
			$getStatusTitle = function(int $status) {
				switch ($status) {
					case User::active:
						return t("active");
					case User::deactive:
						return t("deactive");
					case User::suspend:
						return t("suspend");
				}
			};
			$logs[] = new Log('userpanel_register_type', (isset($options["status"]) and $options["status"]) ? $getStatusTitle($options["status"]) : "-", $getStatusTitle($inputs["userpanel_register_status"]), t('settings.userpanel.register.status'));
			$options["status"] = $inputs["userpanel_register_status"];
		}
		Options::save("packages.userpanel.register", $options, true);
		return $logs;
	}

}
