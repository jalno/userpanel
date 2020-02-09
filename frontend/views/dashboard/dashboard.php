<?php
namespace themes\clipone\views;
use \packages\base;
use \packages\base\events;
use \packages\base\packages;
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
	public static function getSettingsMenu() {
		if (!self::$settingsMenuItem) {
			$item = new menuItem("settings");
			$item->setTitle(translator::trans('settings'));
			$item->setIcon('clip-settings');
			self::$settingsMenuItem = $item;
		}
		return self::$settingsMenuItem;
	}
	protected static $settingsMenuItem;
	public function __beforeLoad(){
		$this->setTitle(translator::trans('dashboard'));
		navigation::active("dashboard");
		$initEvent = new initializeDashboard();
		$initEvent->view = $this;
		events::trigger($initEvent);
		$types = authorization::childrenTypes();
		if(authorization::is_accessed("users_list") and $types){
			$today = date::mktime(0, 0, 0);
			$user = new user();
			$user->where("type", $types, "in");
			$user->where("id", authentication::getID(), "!=");
			$user->where("lastonline", $today, ">=");
			$users = $user->count();
			$shortcut = new shortcut("users");
			$shortcut->icon = "fa fa-users";
			if ($users) {
				$shortcut->title = $users;
				$shortcut->text = translator::trans("shortcut.users.loggined");
			} else {
				$shortcut->text = translator::trans("shortcut.users.loggined.iszero");
			}
			$shortcut->setLink(translator::trans("shortcut.users.link"), userpanel\url("users"));
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
		usort(self::$shortcuts, function($a, $b) {
			return $b->priority - $a->priority;
		});
		foreach (self::$shortcuts as $box) {
			$rows[$lastrow][] = $box;
			$size = 0;
			foreach ($rows[$lastrow] as $rowbox) {
				$size += $rowbox->size;
			}
			if ($size >= 12) {
				$lastrow++;
			}
		}
		$html = '';
		foreach ($rows as $row) {
			$html .= "<div class=\"row\">";
			foreach ($row as $shortcut) {
				$html .= "<div class=\"col-sm-{$shortcut->size}\">";
				$html .= "<div class=\"core-box " . ($shortcut->color ? "box-{$shortcut->color}" : "") . '">';
				$html .= "<div class=\"heading\">";
				$html .= "<i class=\"{$shortcut->icon}\"></i>";
				$html .= "<h2>{$shortcut->title}</h2>";
				if ($shortcut->description) {
					$html .= "<p class=\"box-description\">{$shortcut->description}</p>";
				}
				$html .= "</div>";
				if ($shortcut->text) {
					$html .= "<div class=\"content\">{$shortcut->text}</div>";
				}
				$iconDirection = ((bool)translator::getLang()->isRTL()) ? "left" : "right";
				if (!empty($shortcut->link)) {
					$html .= "<a class=\"view-more\" href=\"".$shortcut->link[1]."\">";
					$html .= "<i class=\"clip-arrow-" . $iconDirection . "-2\"></i> ".$shortcut->link[0]."</a>";
				}
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
		$panel->scroll = true;
		$panel->scrollHeight = 300;
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
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";
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
		$item->setTitle(t("dashboard"));
		$item->setURL(base\url('userpanel'));
		$item->setIcon('clip-home-3');
		$item->setPriority(0);
		navigation::addItem($item);
		navigation::addItem(self::getSettingsMenu());
	}
}
