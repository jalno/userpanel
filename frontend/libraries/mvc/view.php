<?php
namespace themes\clipone;
use \packages\userpanel\frontend;
use \packages\base\date;
trait viewTrait{
	function the_header($template = ''){
		require_once(__DIR__.'/../../header'.($template ? '.'.$template : '').'.php');
	}
	function the_footer($template = ''){
		require_once(__DIR__.'/../../footer'.($template ? '.'.$template : '').'.php');
	}
	function getLogoHTML(){
		$logo = frontend::getLogoHTML();
		if(!$logo){
			$logo = 'CLIP<i class="clip-clip"></i>ONE';
		}
		return $logo;
	}
	function getCopyRightHTML(){
		$copyright = frontend::getCopyRightHTML();
		if(!$copyright){
			$copyright = date::Format('Y').' &copy; clip-one by cliptheme.';
		}
		return $copyright;
	}
}
