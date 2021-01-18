<?php
namespace packages\userpanel\events\settings;

use packages\base\Exception;

class ControllerException extends Exception {

	/**
	 * @var string
	 */
	private $controller;

	public function __construct(string $controller){
		$this->controller = $controller;
	}

	public function getController(): string {
		return $this->controller;
	}
}

class InputNameException extends Exception {

	/**
	 * @var string
	 */
	private $input;

	public function __construct(string $input) {
		$this->input = $input;
	}

	public function getInput(): string {
		return $this->input;
	}

}
