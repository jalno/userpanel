<?php
namespace packages\userpanel\views\users;

use packages\userpanel\{Authorization, Country, views\Form, user\SocialNetwork};

class Edit extends Form {
	use SettingsTrait;

	/** @var bool */
	protected $canEditPrivacy;

	/** @var bool */
	protected $canChangeCredit;

	/** @var bool that indicates viewer user can change permissions of the user */
	protected $canEditPermissions;

	public function __construct() {
		$this->canEditPrivacy = Authorization::is_accessed("profile_edit_privacy");
		$this->canChangeCredit = Authorization::is_accessed("users_edit_credit");
		$this->canEditPermissions = Authorization::is_accessed("users_edit_permissions");
	}
	public function setCountries($countries): void {
		$this->setData($countries, 'countries');
	}
	public function getCountries(): array {
		return $this->getData('countries');
	}
	public function setTypes($types): void {
		$this->setData($types, 'types');
	}
	public function getTypes(): array {
		return $this->getData('types');
	}
	public function setForm(): void {
		$user = $this->getData('user');
		$userArray = $user->toArray();
		$country = $user->country ? $user->country : Country::getDefaultCountry();
		foreach (array("phone", "cellphone") as $field) {
			$item = explode(".", $userArray[$field]);
			$count = count($item);
			$userArray["{$field}[code]"] = $count > 1 ? $item[0] : $country->id;
			$userArray["{$field}[number]"] = $count > 1 ? $item[1] : $item[0];
			unset($userArray[$field]);
		}
		$this->setDataForm($userArray);
		foreach ($user->socialnetworks as $socialnet) {
			$this->setDataForm($socialnet->username, "socialnets[{$socialnet->network}]");
		}
		foreach (array(
			'email',
			'cellphone',
			'phone',
			'socialnetworks_'.socialnetwork::telegram,
			'socialnetworks_'.socialnetwork::instagram,
			'socialnetworks_'.socialnetwork::skype,
			'socialnetworks_'.socialnetwork::twitter,
			'socialnetworks_'.socialnetwork::facebook,
			'socialnetworks_'.socialnetwork::gplus,
		) as $field){
			$this->setDataForm($user->getVisibility($field), 'visibility_'.$field);
		}
	}
}
