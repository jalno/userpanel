<?php
namespace packages\userpanel\events\settings;
use \packages\base\event;
use \packages\userpanel\user;
class tuning{
	private $name;
	private $package;
	private $inputs = [];
	private $fields = [];
	private $controller;
	private $data = [];
	function __construct($name){
		$this->setName($name);
	}
	public function setName(string $name){
		$this->name = $name;
	}
	public function getName():string{
		return $this->name;
	}
	public function addInput(array $input){
		if(!isset($input['name'])){
			throw new inputNameException($input);
		}
		$this->inputs[$input['name']] = $input;
	}
	public function getInputs():array{
		return $this->inputs;
	}
	public function addField(array $field){
		$this->fields[] = $field;
	}
	public function getFields():array{
		return $this->fields;
	}
	public function setController(string $controller){
		list($class, $method) = explode("@", $controller, 2);
		if(!(class_exists($class) and method_exists($class, $method))){
			throw new controllerException($controller);
		}
		$this->controller = [$class, $method];
	}
	public function callController(array $inputs, user $user){
		if($this->controller){
			$class = new $this->controller[0];
			$method = $this->controller[1];
			return $class->$method($inputs, $user);
		}
		return false;
	}
	public function setDataForm(string $name, $value){
		$this->data[$name] = $value;
	}
	public function getDataForm(string $name = ''){
		if($name and isset($this->data[$name])){
			return $this->data[$name];
		}
		return $this->data;
	}
}
