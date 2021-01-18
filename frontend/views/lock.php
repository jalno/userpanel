<?php
namespace themes\clipone\views;

use packages\base\{Packages, frontend\Theme};
use packages\userpanel\views\Lock as ParentView;
use themes\clipone\{ViewTrait, views\FormTrait};

class Lock extends ParentView {
	use ViewTrait, FormTrait;
	/**
	 * @var \packages\userpanel\User
	 */
	protected $user;

	public function __beforeLoad(){
		$this->setTitle(t('unlock'));
		$this->user = $this->getUser();
	}

	protected function getSelfAvatarURL(){
		$user = $this->getUser();
		if($user->avatar){
			return Packages::package('userpanel')->url($user->avatar);
		}
		return Theme::url('assets/images/defaultavatar.jpg');
	}
}
