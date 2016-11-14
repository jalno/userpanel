<?php
namespace packages\userpanel\search;
class link extends result{
	protected $link;
	protected $title;
	protected $description;
	public function setLink($link){
		$this->link = $link;
	}
	public function setTitle($title){
		$this->title = $title;
	}
	public function setDescription($description){
		$this->description = $description;
	}
	public function getLink(){
		return $this->link;
	}
	public function getTitle(){
		return $this->title;
	}
	public function getDescription(){
		return $this->description;
	}
}
