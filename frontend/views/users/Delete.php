<?php
namespace themes\clipone\views\users;

use packages\base\{frontend\Theme};
use packages\userpanel;
use packages\userpanel\{Usertype};
use themes\clipone\{Breadcrumb, views\FormTrait, Navigation, Navigation\MenuItem, ViewTrait};
use packages\userpanel\views\users\Delete as ParentView;

class Delete extends ParentView {
	use FormTrait, ViewTrait;

	protected $usertypes = array();

	public function __beforeLoad(): void {
		$this->setTitle(t("user.delete.warning.title"));

		$this->setNavigation();
	}
	private function setNavigation() {
		$item = new menuItem("users");
		$item->setTitle(t('users'));
		$item->setURL(userpanel\url('users'));
		$item->setIcon('clip-users');
		breadcrumb::addItem($item);

		$item = new menuItem("user");
		$item->setTitle($this->getDataForm('name'));
		$item->setURL(userpanel\url('users/view/'.$this->getDataForm('id')));
		$item->setIcon('clip-user');
		breadcrumb::addItem($item);

		$item = new menuItem("edit");
		$item->setTitle(t('user.delete'));
		$item->setURL(userpanel\url('users/edit/'.$this->getDataForm('id')));
		$item->setIcon('clip-edit');
		breadcrumb::addItem($item);

		navigation::active("users/list");
	}
}
