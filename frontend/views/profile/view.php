<?php
namespace themes\clipone\views\Profile;
use \packages\base\translator;
use \packages\base\frontend\theme;
use packages\base\db;
use packages\base\packages;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\usertype;
use \packages\userpanel\log;
use \packages\userpanel\log_user;
use \packages\userpanel\user\socialnetwork;
use \packages\userpanel\views\profile\view as profileView;

use themes\clipone\{viewTrait, views\BoxyTrait, views\TabTrait, events, Breadcrumb, Navigation};


class view extends profileView{
	use viewTrait, BoxyTrait, TabTrait;
	
	protected $networks = array();
	protected $lastlogin = 0;
	protected $logs = array();
	public function __beforeLoad(){
		$this->setTitle(t('profile.view'));
		$this->loadLastLogin();
		$this->loadSocialnetworks();
		$this->addBodyClass('profile');
		$this->addBodyClass('profile_view');
		$this->setNavigation();
		$initEvent = new events\InitializeProfile();
		$initEvent->view = $this;
		$initEvent->trigger();
		$this->addBox(new ActivityCalendarBox($this->getData('user')));
	}
	private function loadLastLogin(){
		$log = new log();
		$log->where("user", $this->getUserData('id'));
		$log->orderBy("time", "DESC");
		$this->lastlogin = $log->getValue('userpanel_logs.time');
	}
	private function loadSocialnetworks(){
		$networks = $this->getUserData('socialnetworks');
		if($networks){
			foreach($networks as $network){
				if($this->is_public('socialnetworks_'.$network->network)){
					$name = '';
					switch($network->network){
						case(socialnetwork::facebook):$name = 'facebook';break;
						case(socialnetwork::twitter):$name = 'twitter';break;
						case(socialnetwork::gplus):$name = 'google-plus';break;
						case(socialnetwork::instagram):$name = 'instagram';break;
						case(socialnetwork::telegram):$name = 'telegram';break;
						case(socialnetwork::skype):$name = 'skype';break;
					}
					if($name){
						$this->networks[$name] = $network->getURL();
					}
				}
			}
		}
	}
	private function setNavigation(){
		$item = new Navigation\MenuItem("profile");
		$item->setTitle(t('profile.view'));
		$item->setURL(userpanel\url('profile/view'));
		$item->setIcon('clip-user');
		Breadcrumb::addItem($item);
		Navigation::active("dashboard");
	}
	protected function getAvatarURL(){
		if($this->getUserData('avatar')){
			return packages::package('userpanel')->url($this->getUserData('avatar'));
		}else{
			return theme::url('assets/images/defaultavatar.jpg');
		}
	}
	protected function is_public($field){
		$user = $this->getData('user');
		return $user->getVisibility($field);
	}
}
