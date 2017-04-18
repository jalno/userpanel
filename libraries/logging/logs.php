<?php
namespace packages\userpanel;
use \packages\base\db\dbObject;
use \packages\base\http;
class log extends dbObject{
	const login = 1;
	const logout = 2;
	const user_edit = 3;
	const user_delete = 4;
	const register = 'register';
	const loginwrong = 'loginwrong';
	protected $dbTable = "userpanel_logs";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'ip' => array('type' => 'text', 'required' => true),
        'time' => array('type' => 'int', 'required' => true),
        'type' => array('type' => 'text', 'required' => true)
    );
    protected $relations = array(
		'params' => array("hasMany", "packages\\userpanel\\log_param", "log"),
		'users' => array("hasMany", "packages\\userpanel\\log_user", "log")
    );
	public function save($data = null){
		if($this->isNew){
			$params = array();
			$users = array();
			if(!isset($this->data['time']) or !$this->data['time']){
				$this->data['time'] = time();
			}
			if(!isset($this->data['ip']) or !$this->data['ip']){
				if(isset(http::$client['ip'])){
					$this->data['ip'] = http::$client['ip'];
				}elseif(isset(http::$server['ip'])){
					$this->data['ip'] = http::$server['ip'];
				}else{
					$this->data['ip'] = '0.0.0.0';
				}
			}
			if(isset($this->data['params']) and is_array($this->data['params']) and $this->data['params']){
				$params = $this->data['params'];
				unset($this->data['params']);
			}
			if(isset($this->data['users']) and is_array($this->data['users']) and $this->data['users']){
				$users = array_unique(array_merge($users, $this->data['users']));
				unset($this->data['users']);
			}
			if(isset($this->data['user'])){
				if($this->data['user']){
					$users[] = $this->data['user'];
					unset($this->data['user']);
				}elseif($this->data['user'] != null){
					unset($this->data['user']);
				}
			}elseif(empty($users)){
				$users[] = authentication::getID();

			}


			$return = parent::save();
			if($return){
				foreach($users as $user){
					$link = new log_user(array(
						'log' => $return,
						'user' => $user
					));
					$link->save();
				}
				foreach($params as $name => $value){
					$param = new log_param(array(
						'log' => $return,
						'name' => $name,
						'value' => $value
					));
					$param->save();
				}
				return $return;
			}else{
				return false;
			}
		}else{
			return parent::save($data);
		}
	}
}
