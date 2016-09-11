<?php
namespace themes\clipone;
trait viewTrait{
	function the_header($template = ''){
		require_once(__DIR__.'/../../header'.($template ? '.'.$template : '').'.php');
	}
	function the_footer($template = ''){
		require_once(__DIR__.'/../../footer'.($template ? '.'.$template : '').'.php');
	}
}
