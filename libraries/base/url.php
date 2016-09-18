<?php
namespace packages\userpanel;
use \packages\base;
function url($page = '',$parameters = array(),$absolute = false){
	return base\url("userpanel/{$page}", $parameters, $absolute);
}
?>
