<?php
namespace themes\clipone\views;

use packages\userpanel\views\resetpwd as ResetPWDView;
use themes\clipone\{views\FormTrait, ViewTrait};

class resetpwd extends ResetPWDView {
	use ViewTrait, FormTrait;

	function __beforeLoad() {
		$this->setTitle(t("userpanel.resetpwd"));
		$this->addBodyClass('login');
		$this->addBodyClass('resetpwd');
	}
	public function hasBlocked(): bool {
		foreach ($this->getErrors() as $error) {
			if ($error->getCode() == 'userpanel.resetpwd.losingChance') {
				return true;
			}
		}
		return false;
	}
	public function getChannelsOptions() {
		$channelOptions = array();
		$channelNames = $this->getData("channelsnames");
		foreach ($channelNames as $channelName) {
			$channelOptions[] = array(
				"label" => t("notifications.channel.name." . $channelName),
				"value" => $channelName,
			);
		}
		return $channelOptions;
	}
}
