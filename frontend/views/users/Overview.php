<?php
namespace themes\clipone\views\users;
use packages\base\{db, frontend\theme, translator, Packages, Options};
use packages\userpanel;
use packages\userpanel\user;
use packages\userpanel\usertype;
use packages\userpanel\log;
use packages\userpanel\log_user;
use packages\userpanel\user\socialnetwork;
use packages\userpanel\views\users\view as usersView;
use packages\userpanel\Authorization;
use themes\clipone\navigation;
use themes\clipone\navigation\menuItem;
use themes\clipone\breadcrumb;
use themes\clipone\utility;
use themes\clipone\{viewTrait, views\BoxyTrait, views\TabTrait, events, views\Profile\ActivityCalendarBox, views\UserAdditionalinformationTrait};


class Overview extends usersView{
	use viewTrait, BoxyTrait, TabTrait, UserAdditionalinformationTrait;
	protected $networks = array();
	protected $lastlogin = 0;
	protected $lastIP;
	protected $logs = array();
	protected $canEdit = false;

	public function __beforeLoad(){
		$this->user = $this->getData('user');
		$this->setTitle(t("user.profile.overview"));
		$this->loadLastLog();
		$this->loadSocialnetworks();
		$this->setNavigation();
		$this->addBodyClass('users');
		$this->addBodyClass('users_view');
		$initEvent = new events\InitializeProfile();
		$initEvent->view = $this;
		$initEvent->trigger();
		$this->addBox(new ActivityCalendarBox($this->user));
		$this->canEdit = Authorization::is_accessed('profile_edit');
	}

	private function loadLastLog(): void {
		$log = (new Log)
			->where("user", $this->user->id)
			->orderBy("time", "DESC")
			->getOne();
		if (!$log) {
			return;
		}
		$this->lastlogin = $log->time;
		$this->lastIP = $log->ip;
	}

	private function loadSocialnetworks(){
		$networks = $this->getUserData('socialnetworks');
		if ($networks) {
			foreach($networks as $network){
				if($this->is_public('socialnetworks_'.$network->network)){
					$name = '';
					switch ($network->network) {
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
		$item = new menuItem("users");
		$item->setTitle(translator::trans('users'));
		$item->setURL(userpanel\url('users'));
		$item->setIcon('clip-users');
		breadcrumb::addItem($item);

		$item = new menuItem("user");
		$item->setTitle($this->getData('user')->getFullName());
		$item->setURL(userpanel\url('users/view/'.$this->getUserData('id')));
		$item->setIcon('clip-user');
		breadcrumb::addItem($item);
		navigation::active("users/list");
	}
	protected function getAvatarURL(){
		if($this->getUserData('avatar')){
			return packages::package('userpanel')->url($this->getUserData('avatar'));
		}else{
			return theme::url('assets/images/defaultavatar.jpg');
		}
	}
	protected function is_public($field){
		if($this->canViewInvisibles){
			return true;
		}
		$user = $this->getData('user');
		return $user->getVisibility($field);
	}
	protected function getUserCurrency(): string {
		if (Packages::package("financial")) {
			return \packages\financial\Currency::getDefault($this->user)->title;
		} else {
			return Options::get("packages.userpanel.users.credit.currency.title");
		}
	}
}
