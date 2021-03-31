<?php
namespace themes\clipone\Navigation;

class Badge {
	
	private const COLORS = ["success", "warning", "info", "danger", "inverse", "primary"];

	public static function success(string $title): self {
		return new self("success", $title);
	}

	public static function warning(string $title): self {
		return new self("warning", $title);
	}

	public static function info(string $title): self {
		return new self("info", $title);
	}

	public static function danger(string $title): self {
		return new self("danger", $title);
	}

	public static function inverse(string $title): self {
		return new self("inverse", $title);
	}

	public static function primary(string $title): self {
		return new self("primary", $title);
	}

	/**
	 * @var string
	 */
	protected $color;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @param string $color
	 * @param string $title optional
	 * @throws \InvalidArgumentException
	 * @return this
	 */
	public function __construct(string $color, string $title) {
		$this->setColor($color);
		$this->setTitle($title);
	}

	/**
	 * @param string $color
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
	 * @return string
	 */
	public function getColor(): string {
		return $this->color;
	}

	/**
	 * @param string $title
	 * @return void
	 */
	public function setTitle(string $title): void {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function build(): string {

		$class = $this->color == "primary" ? "new" : $this->color;

		return '<span class="badge badge-' . $class . '">' . (string) $this->title . '</span>';
	}
}
