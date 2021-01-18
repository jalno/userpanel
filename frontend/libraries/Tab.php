<?php
namespace themes\clipone;

use packages\base\{IO\File, View, Exception};

class Tab {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string|null
	 */
	protected $link;

	/**
	 * @var string|View
	 */
	protected $view;

	/**
	 * @var View|null
	 */
	protected $parent;

	public function __construct(string $name, $view) {
		$this->name = $name;
		$this->view = $view;
	}

	public function getName(): string {
		return $this->name;
	}

	/**
	 * Set title
	 * 
	 * @param string $title
	 * @return void
	 */
	public function setTitle(string $title): void {
		$this->title = $title;
	}

	/**
	 * Getter for title of tab
	 * 
	 * @return string|null
	 */
	public function getTitle(): ?string {
		return $this->title;
	}

	/**
	 * Set link
	 * 
	 * @param string $link
	 * @return void
	 */
	public function setLink(string $link): void {
		$this->link = $link;
	}

	/**
	 * Getter for link of tab
	 * 
	 * @return string|null
	 */
	public function getLink(): ?string {
		return $this->link;
	}

	/**
	 * Set View
	 * 
	 * @param string|View $view
	 * @return void
	 */
	public function setView($view): void {
		$this->view = $view;
	}

	/**
	 * Getter for veiw
	 * 
	 * @throws Exception
	 * @return View
	 */
	public function getView(): View {
		if (is_string($this->view)) {
			$this->view = View::byName($this->view);
			if ($this->parent) {
				$this->view->setData($this->parent->__getData());
			}
		}
		if (!$this->view) {
			throw new Exception("cannot find view");
		}
		return $this->view;
	}

	/**
	 * Set Parent View
	 * 
	 * @param View|null $parent
	 * @return void
	 */
	public function setParent(?View $parent): void {
		$this->parent = $parent;
	}

	/**
	 * Getter for parent View
	 * 
	 * @return View
	 */
	public function getParent(): ?View {
		return $this->parent;
	}
}
