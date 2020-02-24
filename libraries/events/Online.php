<?php
namespace packages\userpanel\Events;
use packages\base\Event;

class Online extends Event {
	private $response = array();
	/**
	 * @var Mixed $response
	 * @var String $name
	 * @return void
	 */
	public function addResponse($response, string $name = '') {
		if (!$name and !is_array($response)) {
			throw new InvalidArgumentException("response value must be array");
		}
		if (($name and $name == "status") or isset($response["status"])) {
			throw new InvalidArgumentException("can not set status response");
		}
		if ($name) {
			if (isset($this->response[$name])) {
				trigger_error("{$name} response has been set before");
			}
			$this->response[$name] = $response;
		} else {
			array_replace_recursive($this->response, $response);
		}
	}
	/**
	 * @return array
	 */
	public function getResponse(): array {
		return $this->response;
	}
}
