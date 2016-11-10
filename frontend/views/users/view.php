<?php
namespace themes\clipone\views\users;
use \packages\base\translator;
use \packages\base\frontend\theme;
use packages\base\db;
use packages\base\packages;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\usertype;
use \packages\userpanel\log;
use \packages\userpanel\log_user;
use \packages\userpanel\user_socialnetwork;
use \packages\userpanel\views\users\view as usersView;

use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\breadcrumb;
use \themes\clipone\utility;
use \themes\clipone\viewTrait;


class view extends usersView{
	use viewTrait;
	protected $networks = array();
	protected $lastlogin = 0;
	protected $logs = array();
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('users'),
			translator::trans('user.profile'),
			$this->getUserData('name')
		));
		$this->loadLogs();
		$this->loadLastLogin();
		$this->loadSocialnetworks();
		$this->addAssets();
		$this->setNavigation();
	}
	private function loadLogs($number = 50){
		$logsobj = new log();
		$logsobj->join(new log_user(), 'id', 'LEFT', 'log');
		$logsobj->where("userpanel_logs_users.user", $this->getUserData('id'));
		$logsobj->orderBy("userpanel_logs.time", "desc");
		$logs = $logsobj->get($number);
		foreach($logs as $log){
			$params = array();
			if(in_array($log->type, array(log::user_edit, log::user_delete))){
				db::join("userpanel_users u", "u.id=l.value", "left");
				db::where("l.log", $log->id);
				db::where("l.name", "user");
				if($user = db::getOne("userpanel_logs_params l", array("l.value as id", "u.name"))){
					$params['user.name'] = $user['name'] ? '<span class="tooltips" title="#'.$user['id'].'">'.$user['name'].'</span>' : '#'.$user['id'];
				}else{
					continue;
				}
			}
			$text = translator::trans(utility::switchcase($log->type, array(
				'logs.login' => log::login,
				'logs.user_edit' => log::user_edit,
				'logs.user_delete' => log::user_delete
			)), $params);
			$color = utility::switchcase($log->type, array(
				'circle-green' => array(log::login),
				'circle-teal' => array(log::user_edit),
				'circle-bricky' => array(log::user_delete)
			));
			$icon = utility::switchcase($log->type, array(
				'clip-key' => array(log::login),
				'clip-pencil' => array(log::user_edit),
				'fa fa-trash-o' => array(log::user_delete)
			));
			if($text and $color and $icon){
				$this->logs[] = array(
					'id' => $log->id,
					'text' => $text,
					'class' => $color.' '.$icon,
					'time' => $log->time
				);
			}
		}
	}
	private function loadLastLogin(){
		db::join("userpanel_logs_users link", "link.log=l.id", "LEFT");
		db::where("link.user", $this->getUserData('id'));
		db::where("l.type", log::login);
		db::orderBy("l.time", "desc");
		$this->lastlogin = db::getValue("userpanel_logs l", 'l.time');
	}
	private function loadSocialnetworks(){
		$networks = $this->getUserData('socialnetworks');
		if($networks){
			foreach($networks as $network){
				$name = '';
				switch($network->network){
					case(user_socialnetwork::facebook):$name = 'facebook';break;
					case(user_socialnetwork::twitter):$name = 'twitter';break;
					case(user_socialnetwork::gplus):$name = 'google-plus';break;
					case(user_socialnetwork::instagram):$name = 'instagram';break;
					case(user_socialnetwork::telegram):$name = 'telegram';break;
				}
				if($name){
					$this->networks[$name] = $network->url;
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
		$item->setTitle($this->getUserData('name'));
		$item->setURL(userpanel\url('users/view/'.$this->getUserData('id')));
		$item->setIcon('clip-user');
		breadcrumb::addItem($item);
		navigation::active("users/list");
	}
	private function addAssets(){
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css'));
		$this->addCSSFile(theme::url('assets/plugins/bootstrap-social-buttons/social-buttons-3.css'));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js'));
		$this->addJSFile(theme::url('assets/js/pages/users.view.js'));
	}
	protected function getAvatarURL(){
		if($this->getUserData('avatar')){
			return packages::package('userpanel')->url($this->getUserData('avatar'));
		}else{
			return theme::url('assets/images/defaultavatar.jpg');
		}
	}
}
