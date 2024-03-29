<?php
namespace packages\userpanel\logging\Exception;
use packages\userpanel\logging\Exception;
class invalidTypeException extends Exception {
	/**
	 * @var string
	 */
	private $type;

	public function __construct(string $type, string $message = ''){
		parent::__construct($message);
		$this->type = $type;
	}

	public function getType(): string {
		return $this->type;
	}

}