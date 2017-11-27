<?php
namespace packages\userpanel\logs;
use \packages\base;
use \packages\base\{view, translator};
use \packages\userpanel;
use \packages\userpanel\{date, logs\panel, logs};
class login extends logs{
	public function getColor():string{
		return "circle-green";
	}
	public function getIcon():string{
		return "clip-key";
	}
	public function buildFrontend(view $view){}
}
