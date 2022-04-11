<?php
namespace themes\clipone\users;

use packages\base\View;
use themes\clipone\views\FormTrait;

class SubmitModal
{
	use FormTrait;

	const POST = 'post';
	const GET = 'get';

	private View $view;
	private ?string $method = null;
	private ?string $link = null;
	private ?string $icon = null;
	private ?string $id = null;
	private ?string $title = null;
	private ?string $content = null;
	private array $fields = [];

	public function __construct(View $view, string $link = null, ?string $method = null, ?string $id = null) {
		if (!$method) {
			$method = self::POST;
		}

		if (!$id) {
			$id = 'submit-action-button-' . rand(999, 99999);
		}

		$this->view = $view;
		$this->setMethod($method);
		$this->setLink($link);
		$this->setID($id);
	}

	public function setLink(?string $link): void
	{
		$this->link = $link;
	}

	public function getLink(): ?string
	{
		return $this->link;
	}

	public function setMethod(string $method): void
	{
		$method = strtolower($method);

		if (!in_array($method, [self::POST, self::GET])) {
			throw new \InvalidArgumentException("Button method must be one of" . implode(', ', [self::POST, self::GET]));
		}

		$this->method = $method;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function setIcon(?string $icon): void
	{
		$this->icon = $icon;
	}

	public function getIcon(): ?string
	{
		return $this->icon;
	}

	public function setID(string $id): void
	{
		$this->id = $id;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function setContent(string $content): void
	{
		$this->content = $content;
	}

	public function getID(): ?string
	{
		return $this->id;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function addField(array $field): void
	{
		$this->fields[] = $field;
	}

	public function getFields(): array
	{
		return $this->fields;
	}

	public function getHtml(): string
	{
		return '<div class="modal action-button-modal fade" id="' . $this->getID() . '" tabindex="-1" data-show="true" role="dialog">
		<div class="modal-header">
			<h4 class="modal-title">' . $this->getIconHtml() . ' ' . $this->getTitle() . '</h4>
		</div>
		<div class="modal-body">
			<form id="' . $this->getID() . '-form" action="' . $this->link . '" method="POST">' . $this->getContent()  . $this->createFields() . '</form>
		</div>
		<div class="modal-footer">
			<button type="submit" form="' . $this->getID() . '-form" class="btn btn-success">' . t("userpanel.submit") . '</button>
			<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">' . t("userpanel.cancel") . '</button>
		</div>
	</div>';
	}

	public function getFormErrorsByInput(string $name)
	{
		return method_exists($this->view, 'getFormErrorsByInput') ? $this->view->getFormErrorsByInput($name) : null;
	}

	public function getDataForm(string $name)
	{
		return method_exists($this->view, 'getDataForm') ? $this->view->getDataForm($name) : null;
	}

	public function getDataInput(string $name)
	{
		return method_exists($this->view, 'getDataInput') ? $this->view->getDataInput($name) : null;
	}

	protected function createFields(): string
	{
		$html = "";

		foreach ($this->fields as $field) {
			$html .= $this->createField($field, true);
		}

		return $html;
	}

	protected function getIconHtml(): string
	{
		if (!$this->icon) {
			return "";
		}

		return '<i class="' . $this->icon . '"></i>';
	}
}
