<?php
namespace packages\userpanel;

use packages\base\{db\dbObject, http};
use packages\userpanel\{log_param, user, logging\Exception\invalidTypeException};

class Log extends dbObject {
	use Paramable;
	protected $dbTable = "userpanel_logs";
	protected $primaryKey = "id";
	protected $dbFields = [
        'user' => ['type' => 'int', 'required' => true],
        'ip' => ['type' => 'text', 'required' => true],
        'time' => ['type' => 'int', 'required' => true],
        'title' => ['type' => 'text', 'required' => true],
        'type' => ['type' => 'text', 'required' => true],
        'parameters' => ['type' => 'text'],
    ];
	protected $serializeFields = ['parameters'];
    protected $relations = [
		'params' => ["hasMany", log_param::class, "log"],
		'user' => ["hasOne", User::class, "user"]
	];
	private $handler;
	public function preLoad(array $data):array{
		if(!isset($data['time']) or !$data['time']){
			$data['time'] = time();
		}
		if(!isset($data['ip']) or !$data['ip']){
			if(isset(http::$client['ip'])){
				$data['ip'] = http::$client['ip'];
			}elseif(isset(http::$server['ip'])){
				$data['ip'] = http::$server['ip'];
			}else{
				$data['ip'] = '0.0.0.0';
			}
		}
		return $data;
	}
	public function getHandler(){
		if(!$this->handler){
			if(!class_exists($this->type)){
				throw new invalidTypeException($this->type);
			}
			$this->handler = new $this->type;
			$this->handler->setLog($this);
		}
		return $this->handler;
	}
}
