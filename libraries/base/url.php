<?php
namespace packages\userpanel;
use \packages\base;
function url($page = '',$parameters = array()){
	return base\url("userpanel/{$page}", $parameters);
}
?>
