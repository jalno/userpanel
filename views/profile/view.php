<?php
namespace packages\userpanel\views\profile;
use packages\base\{packages, options};
use packages\userpanel\authorization;

class view extends \packages\userpanel\view{
	use settingsTrait;
	public function setUserData($data){
		$this->setData($data, 'user');
	}
	public function getUserData($key){
		return($this->data['user']->$key);
	}
	public function canViewLog():bool{
		return authorization::is_accessed('logs_view');
	}
	public function export(): array {
		$user = $this->getData("user");
		$data = array(
			"data" => array(
				"user" => array(
					"id" => $user->id,
					"name" => $user->name,
					"lastname" => $user->lastname,
					"email" => $user->email,
					"cellphone" => $user->cellphone,
					"phone" => $user->phone,
					"city" => $user->city,
					"country" => $user->country->name,
					"zip" => $user->zip,
					"address" => $user->address,
					"credit" => $user->credit,
					"status" => $user->status,
				),
			),
		);
		if (packages::package("financial")) {
			$data["data"]["user"]["currency"] = \packages\financial\currency::getDefault($user)->title;
		} else {
			$data["data"]["user"]["currency"] = options::get("packages.userpanel.users.credit.currency.title");
		}
		return $data;
	}
}
trait settingsTrait{
	public function canEditSettings():bool{
		return authorization::is_accessed('profile_settings');
	}
	public function setSettings(array $settings){
		$this->setData($settings, 'settings');
	}
	protected function getSettings():array{
		return $this->getData('settings');
	}
}
