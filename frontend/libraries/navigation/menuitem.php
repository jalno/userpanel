<?php
namespace themes\clipone\navigation;
use themes\clipone\breadcrumb;
use \packages\base\http;
class menuItem{
	private $name;
	private $title;
	private $url;
	private $icon;
	private $priority;
	private $items = array();
	private $active;
	function __construct($name){
		$this->name = $name;
	}
	function setTitle($title){
		$this->title = $title;
	}
	function getTitle(){
		return $this->title;
	}
	function setURL($url){
		$this->url = $url;
	}
	function getURL(){
		return $this->url;
	}
	function setIcon($icon){
		$this->icon = $icon;
	}
	function getIcon(){
		return $this->icon;
	}
	function setPriority($priority){
		$this->priority = $priority;
	}
	function getPriority(){
		return $this->priority;
	}
	function getName(){
		return $this->name;
	}
	function addItem(menuItem $item){
		$this->items[$item->getName()] = $item;
	}
	function active($active){
		$this->active = is_string($active) ? explode("/", $active, 2) : $active;
	}
	function build(){
		$thisuri = http::$request['uri'];
		//$active = (substr($thisuri, 0, strlen($this->url)) == $this->url);
		$active = (bool)$this->active;
		$open = ($this->items and $active);
		$html = "<li";
		if($active or $open){
			$html .=" class=\"";
			if($active)$html .='active';
			if($open)$html .=' open';
			$html .="\"";
		}
		$html .="><a href=\"{$this->url}\">".($this->icon ? "<i class=\"{$this->icon}\"></i>" : "")."<span class=\"title\"> {$this->title}</span>".($this->items ? ' <i class="icon-arrow"></i>' : '')."<span class=\"selected\"></span></a>";
		if($this->items){
			$html .= "<ul class=\"sub-menu\">";
			uasort($this->items, array(__NAMESPACE__, 'sort'));
			foreach($this->items as $name => $item){
				if($active and is_array($this->active) and $this->active[0] == $name){
					if(isset($this->active[1]) and $this->active[1])
						breadcrumb::addItem($item);
					$item->active(isset($this->active[1]) ? $this->active[1] : true);
				}
				$html .= $item->build();
			}
			$html .= "</ul>";
		}
		$html .= "</li>";
		return $html;
	}
}
