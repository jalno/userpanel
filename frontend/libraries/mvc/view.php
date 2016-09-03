<?php
namespace themes\clipone;
trait viewTrait{
	function the_header(){
		require_once(__DIR__.'/../../header.php');
	}
	function the_footer(){
		require_once(__DIR__.'/../../footer.php');
	}
}
