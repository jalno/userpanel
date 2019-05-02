<?php
namespace packages\userpanel\events;

use packages\base\{Event, Exception};
use packages\userpanel\Authentication\IHandler;

class AuthenticationHandlersEvent extends Event {
	/**
	 * @var string[] list of handlers class names.
	 */
	protected $handlers = [];

	/**
	 * Getter for handlers list.
	 * 
	 * @return string[]
	 */
	public function getHandlers(): array {
		return $this->handlers;
	}

	/**
	 * Add a new handler to the stack.
	 * 
	 * @param string $className must be a FQCN which implemented IHandler interface.
	 * @throws Exception if given class does not implementing IHandler
	 * @return void
	 */
	public function addHandler(string $className): void {
		if (in_array($className, $this->handlers)) {
			return;
		}
		if (!is_a($className, IHandler::class, true)) {
			throw new Exception("given handler does not implementing " . IHandler::class);
		}
		$this->handlers[] = $className;
	}

	/**
	 * Remove a handler from the list.
	 * 
	 * @param string $className must be FQCN.
	 * @return void
	 */
	public function removeHandler(string $className): void {
		$index = array_search($className, $this->handlers);
		if ($index === false) {
			return;
		}
		array_splice($this->handlers, $index, 1);
	}
}
