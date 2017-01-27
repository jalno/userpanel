<?php
namespace themes\clipone\views;
use \packages\base;
use \packages\base\events;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\authorization;
use \packages\userpanel\views\dashboard as dashboardView;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\views\dashboard\box;
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
	public static function onSourceLoad(){
		$item = new menuItem("dashboard");
		$item->setTitle("پیشخوان");
		$item->setURL(base\url('userpanel'));
		$item->setIcon('clip-home-3');
		navigation::addItem($item);
	}
}
