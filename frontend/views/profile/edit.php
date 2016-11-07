<?php
namespace themes\clipone\views\profile;

use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel\views\profile\edit as profileEditView;
use \packages\userpanel;
use \packages\userpanel\usertype;

use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
class edit extends profileEditView{
	use viewTrait,formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans('profile.edit')
		));

		$this->addAssets();
		$this->setNavigation();
	}
	private function addAssets(){

		$this->addCSSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css'));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js'));
		$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
		$this->addJSFile(theme::url('assets/js/pages/profile.edit.js'));
	}
	private function setNavigation(){
		$item = new menuItem("profile");
		$item->setTitle(translator::trans('profile.view'));
		$item->setURL(userpanel\url('profile/view'));
		$item->setIcon('fa fa-user');
		breadcrumb::addItem($item);


		$editItem = new menuItem("edit");
		$editItem->setTitle(translator::trans('profile.edit'));
		$editItem->setURL(userpanel\url('profile/edit'));
		$editItem->setIcon('fa fa-edit');
		breadcrumb::addItem($editItem);

		navigation::active("dashboard");
	}
	protected function getCountriesForSelect(){
		$options = array();
		foreach($this->getCountries() as $country){
			$options[] = array(
				'title' => $country->name,
				'value' => $country->id
			);
		}
		return $options;
	}
}
