<?php
namespace themes\clipone\views\users;
use \packages\userpanel\views\users\email as usersEmailView;
use \packages\userpanel;
use \packages\userpanel\usertype;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\views\formTrait;
use \packages\base\translator;
use \packages\base\frontend\theme;
class email extends usersEmailView{
	use formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('users'),
			translator::trans('user.sendemail'),
			$this->getDataForm('name')
		));
		$this->setNavigation();
		$this->addAssets();
	}
	private function setNavigation(){
		$item = new menuItem("users");
		$item->setTitle("کاربران");
		$item->setURL(userpanel\url('users'));
		$item->setIcon('clip-users');
		breadcrumb::addItem($item);

		$item = new menuItem("user");
		$item->setTitle($this->getUserData('name'));
		$item->setURL(userpanel\url('users/view/'.$this->getUserData('id')));
		$item->setIcon('clip-user');
		breadcrumb::addItem($item);

		$item = new menuItem("email");
		$item->setTitle("ارسال ایمیل");
		$item->setURL(userpanel\url('users/email/'.$this->getUserData('id')));
		$item->setIcon('fa fa-envelope-o');
		breadcrumb::addItem($item);

		navigation::active("users/list");
	}
	private function addAssets(){
		$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
		$this->addJSFile(theme::url('assets/plugins/ckeditor/ckeditor.js'));
		$this->addJSFile(theme::url('assets/js/users.email.js'));
	}
}
