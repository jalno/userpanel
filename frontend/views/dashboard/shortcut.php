<?php
namespace themes\clipone\views\dashboard;
class shortcut{
	const Danger = "danger";
	const Warning = "warning";
	const Info = "info";
	const Success = "success";
	public $name;
	public $icon;
	public $title;
	public $color;
	public $description;
	public $text;
	public $link;
	public $priority = 0;
	public $size = 4;
	function __construct($name){
		$this->name = $name;
	}
	public function setLink($title, $link){
		$this->link = array($title, $link);
	}
}
