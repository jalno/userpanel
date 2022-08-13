<?php
namespace packages\userpanel\events\settings;

use packages\userpanel\User;

class Tuning {
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var array<string,array<string,mixed>>
	 */
	private $inputs = [];

	/**
	 * @var mixed[]
	 */
	private $fields = [];
	
	/**
	 * @var string|null
	 */
	private $controller;
	
	/**
	 * @var array<string,mixed>
	 */
	private $data = [];

	private string $icon = '';

	public function __construct(string $name, string $icon = '') {
		$this->setName($name);
		$this->setIcon($icon);
	}

	public function setName(string $name): void {
		$this->name = $name;
	}

	public function setIcon(string $icon)
	{
		$this->icon = $icon;
	}

	public function getIcon(): string
	{
		return $this->icon;
	}

	public function getName(): string {
		return $this->name;
	}

	public function addInput(array $input): void {
		if(!isset($input['name'])){
			throw new InputNameException($input);
		}
		$this->inputs[$input['name']] = $input;
	}

	public function getInputs(): array {
		return $this->inputs;
	}

	public function addField(array $field): void {
		$this->fields[] = $field;
	}

	public function getFields(): array {
		return $this->fields;
	}

	public function setController(string $controller): void {
		if (!class_exists($controller) or !((new $controller) instanceof Controller)) {
			throw new ControllerException($controller);
		}
		$this->controller = $controller;
	}

	public function store(array $inputs, User $user) {
		return $this->callController($inputs, $user, "store");
	}

	public function callController(array $inputs, user $user, string $method) {
		if (!$this->controller) {
			return null;
		}
		if (!method_exists($this->controller, $method)) {
			throw new ControllerException($this->controller . '@' . $method);
		}
		return (new $this->controller)->$method($inputs, $user);
	}

	public function setDataForm(string $name, $value): void {
		$this->data[$name] = $value;
	}

	public function getDataForm(string $name = '') {
		if ($name and isset($this->data[$name])) {
			return $this->data[$name];
		}
		return $this->data;
	}
}
