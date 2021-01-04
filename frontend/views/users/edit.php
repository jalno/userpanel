<?php
namespace themes\clipone\views\users;

use packages\base\{translator, packages, frontend\theme, options};
use packages\userpanel;
use packages\userpanel\{Authentication, views\users\Edit as UsersEditView, usertype, Usertype\Permission, Usertype\Permissions};
use themes\clipone\{breadcrumb, navigation, navigation\menuItem, viewTrait, views\formTrait, views\TabTrait};

class Edit extends UsersEditView {
	use ViewTrait, FormTrait, TabTrait;

	protected $usertypes = array();
	private $user;

	public function __beforeLoad(): void {
		$this->user = $this->getData("user");
		$this->dynamicData()->setData("userPermissions", $this->buildPermissionsArray());
		$this->setTitle(t("profile.edit"));
		$this->addBodyClass('users');
		$this->addBodyClass('users_edit');
		$this->setNavigation();
	}
	private function setNavigation(){
		$item = new menuItem("users");
		$item->setTitle(translator::trans('users'));
		$item->setURL(userpanel\url('users'));
		$item->setIcon('clip-users');
		breadcrumb::addItem($item);

		$item = new menuItem("user");
		$item->setTitle($this->getData('user')->getFullName());
		$item->setURL(userpanel\url('users/view/'.$this->getDataForm('id')));
		$item->setIcon('clip-user');
		breadcrumb::addItem($item);

		$item = new menuItem("edit");
		$item->setTitle(translator::trans('user.edit'));
		$item->setURL(userpanel\url('users/edit/'.$this->getDataForm('id')));
		$item->setIcon('clip-edit');
		breadcrumb::addItem($item);

		navigation::active("users/list");
	}
	protected function getCountriesForSelect(): array {
		$options = array();
		foreach($this->getCountries() as $country){
			$options[] = array(
				'title' => $country->name,
				'value' => $country->id
			);
		}
		return $options;
	}
	protected function getTypesForSelect(): array {
		$options = array();
		foreach($this->getTypes() as $type){
			$options[] = array(
				'title' => $type->title,
				'value' => $type->id
			);
		}
		return $options;
	}

	protected function getAvatarURL(): string {
		if ($this->user->avatar) {
			return Packages::package('userpanel')->url($this->user->avatar);
		} else {
			return Theme::url('assets/images/defaultavatar.jpg');
		}
	}
	protected function buildPermissionsArray(bool $withTranslate = false): array {
		$existentPermissions = Permissions::existentForUser(Authentication::getUser());
		$userPermissions = $this->user->getPermissions();
		return array_map(function (string $permission) use ($userPermissions, $withTranslate) {
			$item = array(
				"key" => $permission,
				"value" => in_array($permission, $userPermissions),
			);
			if ($withTranslate) {
				$item["title"] = t("usertype.permissions.{$permission}");
			}
			return $item;
		}, $existentPermissions);
	}
	protected function getFieldPrivacyGroupBtn($field){
		if(!$this->canEditPrivacy){
			return false;
		}
		$privacy = $this->getDataForm('visibility_'.$field);
		$button = array(
			'type' => 'button',
			'icon' => $privacy ? 'fa fa-eye' : 'fa fa-eye-slash',
			'text' => translator::trans('user.edit.privacy.'.($privacy ? 'public' : 'private')),
			'class' => array('btn','btn-default'),
			'dropdown' => array()
		);

		$button['dropdown'][] = array(
			'icon' => 'fa fa-eye',
			'link' => '#',
			'class' => array('changevisibity'),
			'data' => array(
				'field' => $field,
				'visibility' => 'public'
			),
			'title' => translator::trans('user.edit.privacy.public')
		);
		$button['dropdown'][] = array(
			'icon' => 'fa fa-eye-slash',
			'link' => '#',
			'class' => array('changevisibity'),
			'data' => array(
				'field' => $field,
				'visibility' => 'private'
			),
			'title' => translator::trans('user.edit.privacy.private')
		);
		return array(
			'last' => array($button)
		);
	}
	protected function getUserCurrency(): string {
		if (packages::package("financial")) {
			return \packages\financial\currency::getDefault($this->user)->title;
		} else {
			return options::get("packages.userpanel.users.credit.currency.title");
		}
	}
}
