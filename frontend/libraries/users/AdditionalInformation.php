<?php
namespace themes\clipone\users;

class AdditionalInformation {

	/** @var string */
	private $name;

	/** @var string|int */
	private $value;

	/** @var array<string> */
	private $classes = array();

	/** @var array<mixed> */
	private $data = array();

	/**
	 * @param string $name
	 * @param string|int $value
	 * @throws \InvalidArgumentException if value data type is invalid
	 */
	public function __construct(string $name, $value) {
		if (!is_string($value) and !is_numeric($value)) {
			throw new \InvalidArgumentException("value data type is invalid");
		}
		$this->setName($name);
		$this->setValue($value);
	}
	/**
	 * @param string $name
	 * @return void
	 */
	public function setName(string $name): void {
		$this->name = $name;
	}
	/**
	 * @param string|int $value
	 * @throws \InvalidArgumentException if value data type is invalid
	 * @return void
	 */
	public function setValue($value): void {
		if (!is_string($value) and !is_numeric($value)) {
			throw new \InvalidArgumentException("value data type is invalid");
		}
		$this->value = $value;
	}
	public function addClass(string $class): void {
		$this->classes[] = $class;
	}
	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function addData(string $name, $value): void {
		$this->data[$name] = $value;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return string|int
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function getClasses(): string {
		return implode(" ", $this->classes);
	}

	/**
	 * @return array<mixed>
	 */
	public function getData(): array {
		return $this->data;
	}
}
