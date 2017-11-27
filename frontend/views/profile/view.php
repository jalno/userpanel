<?php
namespace themes\clipone\views\profile;
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

use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\breadcrumb;
use \themes\clipone\utility;
use \themes\clipone\viewTrait;


class view extends profileView{
	use viewTrait;
	protected $networks = array();
	protected $lastlogin = 0;
	protected $logs = array();
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('profile.view')
		));
		$this->loadLastLogin();
		$this->loadSocialnetworks();
		$this->addBodyClass('profile');
		$this->addBodyClass('profile_view');
		$this->setNavigation();
	}
	protected function loadLogs($number = 50){
		$logsobj = new log();
		$logsobj->where("user", $this->getUserData('id'));
		$logsobj->orderBy("userpanel_logs.time", "DESC");
		return $logsobj->get($number);
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
		$item = new menuItem("profile");
		$item->setTitle(translator::trans('profile.view'));
		$item->setURL(userpanel\url('profile/view'));
		$item->setIcon('clip-user');
		breadcrumb::addItem($item);
		navigation::active("dashboard");
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
