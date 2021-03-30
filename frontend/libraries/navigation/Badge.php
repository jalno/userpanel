<?php
namespace themes\clipone\Navigation;

class Badge {
	
	private const COLORS = ["success", "warning", "info", "danger", "inverse", "primary"];

	/**
	 * @var string
	 */
	private $color;

	/**
	 * @var string|int
	 */
	private $title;

	/**
	 * @var string $color
	 * @var string|int $title optional
	 * @throws \InvalidArgumentException
	 * @return this
	 */
	public function __construct(string $color, $title = null) {

		$this->setColor($color);

		if ($title) {
			$this->setTitle($title);
		}
	}

	/**
	 * @var string $color
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setColor(string $color): void {

		if (!in_array($color, self::COLORS)) {
			throw new \InvalidArgumentException($color . " is invalid. valid colors is: [" . implode(", ", self::COLORS) . "]");
		}
		
		$this->color = $color;
	}

	/**
	 * @var string|int $title
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setTitle($title): void {

		if (!is_string($title) and !is_numeric($title)) {
			throw new \InvalidArgumentException("Title is invalid.");
		}

		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function build(): string {

		$class = $this->color == "primary" ? "new" : $this->color;

		return '<span class="badge badge-' . $class . '">' . (string) $this->title . '</span>';
	}
}
