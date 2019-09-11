<?php
namespace themes\clipone;

use packages\base;
use packages\base\{Date, view\Error, Packages, frontend\Theme, Translator};
use packages\userpanel;
use packages\userpanel\{Authentication, Authorization, Frontend};

trait ViewTrait {
	protected $bodyClasses = array('rtl');

	/** @var bool */
	protected $fixedHeader = true;

	/**
	 * location of favicon
	 * @var string
	 */
	protected $favicon = "";

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

	/**
	 * Set Favicon for userpanel pages
	 * @param string $icon must be full path to icon file
	 */
	public function setFavicon(string $icon): void {
		$this->favicon = $icon;
	}

	/**
	 * return full path of icon file
	 * @return string that is full path to favicon logo or empty string if not set before.
	 */
	public function getFavicon(): string {
		return $this->favicon;
	}

	public function addBodyClass($class){
		$this->bodyClasses[] = $class;
	}

	public function removeBodyClass($class){
		if(($key = array_search($class, $this->bodyClasses)) !== false){
			unset($this->bodyClasses[$key]);
		}
	}

	public function fixHeader(bool $fix = true): void {
		$this->fixedHeader = $fix;
	}

	public function staticHeader(bool $static = true): void {
		$this->fixedHeader = !$static;
	}

	public function isFixedHeader(): bool {
		return $this->fixedHeader;
	}

	protected function genBodyClasses(){
		return implode(' ', $this->bodyClasses);
	}

	protected function getErrorsHTML(){
		$code = '';
		foreach($this->getErrors() as $error){
			$alert = array(
				'type' => 'info',
				'txt' => $error->getMessage(),
				'title' => ''
			);
			$data  = $error->getData();
			if(!is_array($data)){
				$data = array();
			}
			$alert = array_merge($alert, $data);
			if(!$alert['txt']){
				if($translator = translator::trans('error.'.$error->getCode())){
					$alert['txt'] = $translator;
				}else{
					$alert['txt'] = $error->getCode();
				}
			}
			switch($error->getType()){
				case(error::FATAL):
					$alert['type'] = 'danger';
					if(!$alert['title'])
						$alert['title'] = translator::trans('error.'.error::FATAL.'.title');
					break;
				case(error::WARNING):
					$alert['type'] = 'warning';
					if(!$alert['title'])
						$alert['title'] = translator::trans('error.'.error::WARNING.'.title');
					break;
				case(error::NOTICE):
					$alert['type'] = 'info';
					if(!$alert['title'])
						$alert['title'] = translator::trans('error.'.error::NOTICE.'.title');
					break;
				case(error::SUCCESS):
					$alert['type'] = 'success';
					if(!$alert['title'])
						$alert['title'] = translator::trans('error.'.error::NOTICE.'.title');
					break;
			}
			if(isset($alert['classes'])){
				if(is_array($alert['classes'])){
					$alert['classes'] = implode(" ", $alert['classes']);
				}
			}else{
				$alert['classes'] = '';
			}
			$code .= "<div class=\"alert alert-block alert-{$alert['type']} {$alert['classes']}\"";
			$code .= $this->buildAlertHtmlData($alert);
			$code .= "><button data-dismiss=\"alert\" class=\"close\" type=\"button\">&times;</button>";
			$code .= "<h4 class=\"alert-heading\">";
			switch($alert['type']){
				case('danger'):$code.="<i class=\"fa fa-times-circle\"></i>";break;
				case('success'):$code.="<i class=\"fa fa-check-circle\"></i>";break;
				case('info'):$code.="<i class=\"fa fa-info-circle\"></i>";break;
				case('warning'):$code.="<i class=\"fa fa-exclamation-triangle\"></i>";break;
			}

			$code .= " {$alert['title']}</h4><p>{$alert['txt']}</p>";

			if(isset($alert['btns']) and $alert['btns']){
				$code .= "<p>";
				foreach($alert['btns'] as $btn){
					$code .= "<a href=\"{$btn['link']}\" class=\"btn {$btn['type']}\">{$btn['txt']}</a> ";
				}
				$code .= "</p>";
			}
			$code .= "</div>";
		}

		return $code;
	}

	protected function canViewProfile(){
		return authorization::is_accessed('profile_view');
	}

	protected function getSelfAvatarURL(){
		$user = authentication::getUser();
		if($user->avatar){
			return packages::package('userpanel')->url($user->avatar);
		}else{
			return theme::url('assets/images/defaultavatar.jpg');
		}
	}

	private function buildAlertHtmlData($alert){
		$code = "";
		if(isset($alert['data']) and $alert['data']){
			foreach($alert['data'] as $key => $value){
				$code .= " data-{$key}=\"{$value}\" ";
			}
		}
		return $code;
	}
}
