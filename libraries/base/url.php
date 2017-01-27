<?php
namespace packages\userpanel;
use \packages\base;
use \packages\base\packages;
function url($page = '',$parameters = array(),$absolute = false){
	$prefix = packages::package('userpanel')->getOption('urlPrefix');
	if($prefix === null){
		$prefix = 'userpanel';
	}
	if($prefix){
		$prefix .= "/";
	}
	return base\url($prefix.$page, $parameters, $absolute);
}
