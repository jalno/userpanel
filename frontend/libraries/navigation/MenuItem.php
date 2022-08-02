<?php
namespace themes\clipone\Navigation;

use packages\base\{http, Events};
use themes\clipone\{events\navigation as navigationEvents, Breadcrumb};

class MenuItem {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var string
	 */
	private $icon;

	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var array<MenuItem>
	 */
	private $items = array();

	/**
	 * @var array<string>
	 */
	private $active;

	/**
	 * @var bool
	 */
	private $newTab = false;

	/**
	 * @var Badge
	 */
	private $badge;

	private ?MenuItem $parent = null;

	public function __construct($name) {
		$this->name = $name;
	}
	public function setTitle($title): void {
		$this->title = $title;
	}
	public function getTitle(): ?string {
		return $this->title;
	}
	public function setURL($url): void {
		$this->url = $url;
	}
	public function getURL(): ?string {
		return $this->url;
	}
	public function setIcon($icon): void {
		$this->icon = $icon;
	}
	public function getIcon(): ?string {
		return $this->icon;
	}
	public function setPriority($priority): void {
		$this->priority = $priority;
	}
	public function getPriority(): ?int {
		return $this->priority;
	}
	public function getName(): string {
		return $this->name;
	}
	public function addItem(menuItem $item): void {
		$this->items[$item->getName()] = $item;
		$item->setParent($this);
	}
	public function getByName($name): ?MenuItem {
		if(substr($name, -1) == '/'){
			$name = substr($name, 0, strlen($name)-1);
		}
		$names = explode("/", $name, 2);
		$name = $names[0];
		foreach($this->items as $item){
			if($item->getName() == $name){
				if(isset($names[1]) and $names[1]){
					return $item->getByName($names[1]);
				}else{
					return $item;
				}
			}
		}
		return null;
	}
	public function active($active): void {
		$this->active = is_string($active) ? explode("/", $active, 2) : $active;
	}
	public function isEmpty(): bool {
		return empty($this->items);
	}
	public function getItems(): array {
		return $this->items;
	}
	public function removeItem(menuItem $item): bool {
		foreach($this->items as $x => $i){
			if($i->getName() == $item->getName()){
				unset($this->items[$x]);
				return true;
			}
		}
		return false;
	}
	public function setNewTab(bool $newTab = true): void {
		$this->newTab = $newTab;
	}
	public function getNewTab(): bool {
		return $this->newTab;
	}
	public function setBadge(Badge $badge): void {
		$this->badge = $badge;
	}
	public function getBadge(): ?Badge {
		return $this->badge;
	}

	public function setParent(?MenuItem $parent = null)
	{
		$this->parent = $parent;
	}

	public function getParent(): ?MenuItem
	{
		return $this->parent;
	}

	public function build(): string {
		Events::trigger(new navigationEvents\menuItem\Build($this));
		$thisuri = http::$request['uri'];
		$active = (bool)$this->active;
		$open = ($this->items and $active);
		$html = "<li";
		if($active or $open){
			$html .=" class=\"";
			if($active)$html .='active';
			if($open)$html .=' open';
			$html .="\"";
		}
		$newTab = $this->getNewTab() ? ' target="_blank"' : "";
		$html .="><a href=\"{$this->url}\"{$newTab}>" . ($this->icon ? "<i class=\"{$this->icon}\"></i>" : "") . "<span class=\"title\">{$this->title}</span>"  . ($this->items ? ' <i class="icon-arrow"></i>' : '') . ($this->badge ? $this->badge->build() : '') . "<span class=\"selected\"></span></a>";
		if($this->items){
			$html .= "<ul class=\"sub-menu\">";
			uasort($this->items, array(__NAMESPACE__, 'sort'));
			foreach($this->items as $name => $item){
				if($active and is_array($this->active) and $this->active[0] == $name){
					Breadcrumb::addItem($item);
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
