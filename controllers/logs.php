<?php
namespace packages\userpanel\controllers;
use \packages\base;
use \packages\base\{db, db\parenthesis, inputValidation, db\duplicateRecord, response};
use \packages\userpanel;
use \packages\userpanel\{date, user, authorization, authentication, view, views, log, controller};

class logs extends controller{
	protected $authentication = true;
	public function search(){
		authorization::haveOrFail('logs_search');
		$view = view::byName(views\logs\search::class);
		$types = authorization::childrenTypes();
		$log = new log();
		$inputsRules = [
			'id' => [
				'type' => 'number',
				'optional' => true,
				'empty' => true
			],
			'user' => [
				'type' => 'number',
				'optional' => true,
				'empty' => true
			],
			'title' => [
				'type' => 'string',
				'optional' => true,
				'empty' => true
			],
			'timeFrom' => [
				'type' => 'date',
				'optional' => true,
				'empty' => true
			],
			'timeUntil' => [
				'type' => 'date',
				'optional' => true,
				'empty' => true
			],
			'comparison' => [
				'values' => ['equals', 'startswith', 'contains'],
				'default' => 'contains',
				'optional' => true
			]
		];
		$this->response->setStatus(true);
		try{
			$inputs = $this->checkinputs($inputsRules);
			if(isset($inputs['user']) and $inputs['user']){
				if(!user::byId($inputs['user'])){
					throw new inputValidation("user");
				}
			}
			if(isset($inputs['timeFrom'])){
				if($inputs['timeFrom']){
					$inputs['timeFrom'] = date::strtotime($inputs['timeFrom']);
				}else{
					unset($inputs['timeFrom']);
				}
			}
			if(isset($inputs['timeUntil'])){
				if($inputs['timeUntil']){
					$inputs['timeUntil'] = date::strtotime($inputs['timeUntil']);
				}else{
					unset($inputs['timeUntil']);
				}
			}
			if(isset($inputs['timeFrom'])){
				$log->where("time", $inputs['timeFrom'], ">");
			}
			if(isset($inputs['timeUntil'])){
				$log->where("time", $inputs['timeUntil'], "<");
			}
			foreach(['id', 'user', 'title'] as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, ['id', 'user'])){
						$comparison = 'equals';
					}
					$log->where("userpanel_logs.{$item}", $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(['title'] as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where("userpanel_logs.{$item}", $inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				$log->where($parenthesis);
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		db::join("userpanel_users", "userpanel_users.id=userpanel_logs.user", "INNER");
		if($types){
			db::where("userpanel_users.type", $types, 'in');
		}else{
			db::where("userpanel_users.id", authentication::getID());
		}
		$log->orderBy('id', 'DESC');
		$log->pageLimit = $this->items_per_page;
		$logs = $log->paginate($this->page, 'userpanel_logs.*');
		$view->setDataList($logs);
		$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function view(array $data):response{
		authorization::haveOrFail('logs_view');
		$types = authorization::childrenTypes();
		$user = authentication::getUser();
		$log = new log();
		db::join("userpanel_users", "userpanel_users.id=userpanel_logs.user", "INNER");
		if($types){
			$log->where("userpanel_users.type", $types, 'in');
		}else{
			$log->where("userpanel_users.id", $user->id);
		}
		$log->where("userpanel_logs.id", $data['log']);
		if(!$log = $log->getOne("userpanel_logs.*")){
			throw new NotFound();
		}
		$view = view::byName(views\logs\view::class);
		$view->setUser($user);

		$log->getHandler()->buildFrontend($view);
		$view->setLog($log);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function delete(array $data):response{
		authorization::haveOrFail('logs_delete');
		$types = authorization::childrenTypes();
		$log = new log();
		db::join("userpanel_users", "userpanel_users.id=userpanel_logs.user", "INNER");
		if($types){
			$log->where("userpanel_users.type", $types, 'in');
		}else{
			$log->where("userpanel_users.id", authentication::getID());
		}
		$log->where("userpanel_logs.id", $data['log']);
		if(!$log = $log->getOne("userpanel_logs.*")){
			throw new NotFound();
		}
		$view = view::byName(views\logs\delete::class);
		$view->setLog($log);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function terminate(array $data):response{
		authorization::haveOrFail('logs_delete');
		$types = authorization::childrenTypes();
		$log = new log();
		db::join("userpanel_users", "userpanel_users.id=userpanel_logs.user", "INNER");
		if($types){
			$log->where("userpanel_users.type", $types, 'in');
		}else{
			$log->where("userpanel_users.id", authentication::getID());
		}
		$log->where("userpanel_logs.id", $data['log']);
		if(!$log = $log->getOne("userpanel_logs.*")){
			throw new NotFound();
		}
		$view = view::byName(views\logs\delete::class);
		$view->setLog($log);
		try{
			$this->response->setStatus(false);
			$log->delete();
			$this->response->setStatus(true);
			$this->response->Go(userpanel\url("logs/search"));
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
		}
		$this->response->setView($view);
		return $this->response;
	}
}
