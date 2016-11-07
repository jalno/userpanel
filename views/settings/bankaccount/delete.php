<?php
namespace packages\userpanel\views\settings\bankaccount;
use \packages\userpanel\views\listview as list_view;
use \packages\userpanel\authorization;
use \packages\userpanel\settings\bank_account;
use \packages\base\views\traits\form as formTrait;
class delete extends  list_view{
	use formTrait;
	protected $bankaccount;
	public function setBankaccount(bank_account $bankaccount){
		$this->bankaccount = $bankaccount;
	}
	public function getBankaccount(){
		return $this->bankaccount;
	}
}
