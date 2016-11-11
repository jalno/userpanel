<?php
namespace packages\userpanel\search;
abstract class result{
	abstract public function getLink();
	abstract public function getTitle();
	abstract public function getDescription();
}
