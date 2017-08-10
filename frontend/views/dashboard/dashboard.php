<?php
namespace themes\clipone\views;
use \packages\base;
use \packages\base\events;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;
use \packages\userpanel\date;
use \packages\userpanel\user;
use \packages\userpanel\views\dashboard as dashboardView;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\views\dashboard\box;
use \themes\clipone\views\dashboard\panel;
use \themes\clipone\views\dashboard\shortcut;
use \themes\clipone\events\initializeDashboard;
class dashboard extends dashboardView{
	use viewTrait;
	public static $shortcuts = array();
	public static $boxs = array();
	public function __beforeLoad(){
		$this->setTitle(translator::trans('dashboard'));
		navigation::active("dashboard");
		$initEvent = new initializeDashboard();
		$initEvent->view = $this;
		events::trigger($initEvent);
		if(authorization::is_accessed('users_list')){
			$shortcut = new shortcut("users");
			$shortcut->icon = 'fa fa-users';
			$shortcut->color = shortcut::green;
			$shortcut->title = translator::trans('shortcut.users.title');
			$shortcut->text = translator::trans('shortcut.users.text');
			$shortcut->setLink(translator::trans('shortcut.users.link'), userpanel\url('users'));
			self::addShortcut($shortcut);
			self::addBox($this->createOnlineUsers());
		}
	}
	public static function addShortcut(shortcut $shortcut){
		foreach(self::$shortcuts as $key => $item){
			if($item->name == $shortcut->name){
				self::$shortcuts[$key] = $shortcut;
				return;
			}
		}
		self::$shortcuts[] = $shortcut;
	}
	public static function addBox(box $box){
		self::$boxs[] = $box;
	}
	public function getBoxs(){
		return self::$boxs;
	}
	public function generateShortcuts(){
		$rows = array();
		$lastrow = 0;
		$shortcuts = array_slice(self::$shortcuts, 0, max(3, floor(count(self::$shortcuts)/2)));
		foreach($shortcuts as $box){
			$rows[$lastrow][] = $box;
			$size = 0;
			foreach($rows[$lastrow] as $rowbox){
				$size += $rowbox->size;
			}
			if($size >= 12){
				$lastrow++;
			}
		}
		$html = '';
		foreach($rows as $row){
			$html .= "<div class=\"row\">";
			foreach($row as $shortcut){
				$html .= "<div class=\"col-sm-{$shortcut->size}\">";
				$html .= "<div class=\"core-box\">";
				$html .= "<div class=\"heading\">";
				$html .= "<i class=\"{$shortcut->icon} circle-icon circle-{$shortcut->color}\"></i>";
				$html .= "<h2>{$shortcut->title}</h2>";
				$html .= "</div>";
				$html .= "<div class=\"content\">{$shortcut->text}</div>";
				$html .= "<a class=\"view-more\" href=\"".$shortcut->link[1]."\"><i class=\"clip-arrow-left-2\"></i> ".$shortcut->link[0]."</a>";
				$html .= "</div>";
				$html .= "</div>";
			}
			$html .= "</div>";
		}
		return $html;
	}
	public function generateRows(){
		$rows = array();
		$lastrow = 0;
		foreach(self::$boxs as $box){
			$rows[$lastrow][] = $box;
			$size = 0;
			foreach($rows[$lastrow] as $rowbox){
				$size += $rowbox->size;
			}
			if($size >= 12){
				$lastrow++;
			}
		}
		$html = '';
		foreach($rows as $row){
			$html .= "<div class=\"row\">";
			foreach($row as $box){
				$html .= "<div class=\"col-md-{$box->size}\">".$box->getHTML()."</div>";
			}
			$html .= "</div>";
		}
		return $html;
	}
	protected function createOnlineUsers(){
		$panel = new panel("users_online");
		$panel->size = 5;
		$panel->title = translator::trans('users.online');
		$panel->icon = 'fa fa-users';
		$html  = "<table class=\"table table-condensed table-hover\">";
		$html .= "<thead><tr>";
			$html .= "<th>".translator::trans('user.avatar')."</th>";
			$html .= "<th>".translator::trans('user.name')."</th>";
			$html .= "<th></th>";
		$html .= "</tr></thead>";
		$html .= "<tbody>";
		$types = authorization::childrenTypes();
		$user = new user();
		if($types){
			$user->where("type", $types, 'in');
		}else{
			$user->where("id", authentication::getID());
		}
		$user->where('lastonline', date::time() - user::onlineTimeout, '>=');
		foreach($user->get() as $user){
			$html .= "<tr>";
			$html .= "<td><img src=\"".$this->getAvatarURL($user)."\" class=\"img-responsive\" width=\"50\" height=\"50\" alt=\"User #{$user->id}\"></td>";
			$html .= "<td>".$user->getFullName()."</td>";
			$html .= "<td>";
			if(authorization::is_accessed('users_view')){
				$html .= "<a href=\"".userpanel\url('users/view/'.$user->id)."\" class=\"btn btn-xs btn-green tooltips\" title=\"".translator::trans('user.profile')."\"><i class=\"fa fa-credit-card\"></i></a>";
			}
			$html .= "</td>";
			$html .= "</tr></tbody>";
			$html .= "</table>";
		}
		$panel->setHTML($html);
		return $panel;
	}
	protected function getAvatarURL(user $user){
		if($user->avatar){
			return packages::package('userpanel')->url($user->avatar);
		}else{
			return theme::url('assets/images/defaultavatar.jpg');
		}
	}
	public static function onSourceLoad(){
		$item = new menuItem("dashboard");
		$item->setTitle("پیشخوان");
		$item->setURL(base\url('userpanel'));
		$item->setIcon('clip-home-3');
		navigation::addItem($item);
		$item = new menuItem("settings");
		$item->setTitle(translator::trans('settings'));
		$item->setIcon('clip-settings');
		navigation::addItem($item);
	}
}
