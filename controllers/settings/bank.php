<?php
namespace packages\userpanel\controllers\settings;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;
use \packages\base\view\error;
use \packages\base\inputValidation;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\view;
use \packages\userpanel\usertype;
use \packages\userpanel\controller;
use \packages\userpanel\authorization;
use \packages\userpanel\authentication;
use \packages\userpanel\usertype\permissions;
use \packages\userpanel\usertype\permission;
use \packages\userpanel\usertype\priority;
use \packages\userpanel\settings\bank_account;

/**
  * Handler for usertypes
  * @author Mahdi Abedi <abedi@jeyserver.com>
  * @copyright 2016 JeyServer
  */
class bank extends controller{
	/**
	* @var bool require authentication
	*/
	protected $authentication = true;

	/**
	* Search and listing for usertypes
	* @throws inputValidation for input validation
	* @throws inputValidation if id value is not in childrenTypes
	* @return \packages\base\response
	*/
	public function listAcoounts(){
		authorization::haveOrFail('settings_bankaccounts_list');
		$view = view::byName("\\packages\\userpanel\\views\\settings\\bankaccount\\listview");
		$types = authorization::childrenTypes();

		$bankaccount = new bank_account();

		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true,
			),
			'title' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true,
			),
			'word' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'comparison' => array(
				'values' => array('equals', 'startswith', 'contains'),
				'default' => 'contains',
				'optional' => true
			)
		);
		try{
			$inputs = $this->checkinputs($inputsRules);

			//checking id for being on children types
			if(isset($inputs['id']) and $inputs['id']){
				if(!in_array($inputs['id'], $types)){
					throw new inputValidation("id");
				}
			}

			//notmal search
			foreach(array('id', 'bank') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id'))){
						$comparison = 'equals';
					}
					$bankaccount->where($item, $inputs[$item], $comparison);
				}
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}

		//refill the search form
		$view->setDataForm($this->inputsvalue($inputsRules));

		//query with respect for pagination process
		//$userpanel_settings_usertypes_edit->pageLimit = $this->items_per_page;
		$bankaccounts = $bankaccount->paginate($this->page);
		$this->total_pages = $bankaccount->totalPages;
		$view->setDataList($bankaccounts);
		$view->setPaginate($this->page, $bankaccount->totalCount, $this->items_per_page);

		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
}
