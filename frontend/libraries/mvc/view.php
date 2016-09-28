<?php
namespace themes\clipone;
use \packages\userpanel\frontend;
use \packages\base\date;
trait viewTrait{
	protected $bodyClasses = array('rtl');
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
	public function addBodyClass($class){
		$this->bodyClasses[] = $class;
	}
	public function removeBodyClass($class){
		if(($key = array_search($class, $this->bodyClasses)) !== false){
			unset($this->bodyClasses[$key]);
		}
	}
	protected function genBodyClasses(){
		return implode(' ', $this->bodyClasses);
	}
}
