<?php
namespace themes\clipone\users;

use packages\base\View;

class AdditionalInformation {

	/** @var string */
	protected $name;

	/** @var string|int */
	protected $value;

	/** @var array<string> */
	protected $classes = array();

	/** @var array<mixed> */
	protected $data = array();

	protected ?View $view = null;

	/**
	 * @param string $name
	 * @param string|int $value
	 * @throws \InvalidArgumentException if value data type is invalid
	 */
	public function __construct(string $name, $value = null) {
		$this->setName($name);
		if ($value) {
			$this->setValue($value);
		}
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

	public function removeClass(string $class): void
	{
		$key = array_search($class, $this->classes);

		if ($key !== false) {
			array_splice($this->classes, $key, 1);
		}
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
	 * @return mixed|null
	 */
	public function removeData(string $name)
	{
		$data = $this->data[$name] ?? null;

		unset($this->data[$name]);

		return $data;
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

	public function getHtml(): string
	{
		return '
		<tr' . (!empty($this->classes) ? ' class="' . $this->getClasses() . '"' : '') . (!empty($this->data) ? ' ' . $this->generateItemData() : '') . '>
			<td>' . $this->getName() . '</td>
			<td>' . $this->getValue() . '</td>
		</tr>';
	}

	public function generateItemData(?array $data = null, ?string $parentKey = null): string {
		if (is_null($data)) {
			$data = $this->getData();
		}
		$items = array();
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$items[] = $this->generateItemData($value, $key);
			} else {
				$items[] = 'data-' . ($parentKey ? $parentKey . '-' : '') . $key . '="' . (htmlentities(is_array($value) ? encode($value) : $value)) . '"';
			}
		}
		return implode(" ", $items);
	}

	public function setView(View $view): void
	{
		$this->view = $view;
	}

	public function getView(): ?View
	{
		return $this->view;
	}
}
