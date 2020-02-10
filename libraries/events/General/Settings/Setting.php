<?php
namespace packages\userpanel\events\General\Settings;
use packages\base\event;

class Setting {
	private $name;
	private $package;
	private $inputs = [];
	private $fields = [];
	private $controller;
	private $data = [];
	function __construct($name) {
		$this->setName($name);
	}
	public function setName(string $name) {
		$this->name = $name;
	}
	public function getName():string {
		return $this->name;
	}
	public function addInput(array $input) {
		if (!isset($input['name'])) {
			throw new InputNameRequiredException($input);
		}
		$this->inputs[$input['name']] = $input;
	}
	public function getInputs():array {
		return $this->inputs;
	}
	public function addField(array $field) {
		$this->fields[] = $field;
	}
	public function getFields():array {
		return $this->fields;
	}
	public function setController(string $controller) {
		if (!class_exists($controller) or !((new $controller) instanceof Controller)) {
			throw new ControllerException($controller);
		}
		$this->controller = $controller;
	}
	public function store(array $inputs) {
		return $this->callController($inputs, "store");
	}
	public function callController(array $inputs, string $method) {
		if (!$this->controller) {
			return null;
		}
		if (!method_exists($this->controller, $method)) {
			throw new ControllerException($controller);
		}
		return (new $this->controller)->$method($inputs);
	}
	public function setDataForm(string $name, $value) {
		$this->data[$name] = $value;
	}
	public function getDataForm(string $name = '') {
		if ($name) {
			return $this->data[$name] ?? null;
		}
		return $this->data;
	}
}
