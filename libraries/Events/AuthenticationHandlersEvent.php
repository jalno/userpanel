<?php

namespace packages\userpanel\Events;

use packages\base\Event;
use packages\base\Exception;
use packages\userpanel\Authentication\IHandler;

class AuthenticationHandlersEvent extends Event
{
    /**
     * @var string[] list of handlers class names
     */
    protected $handlers = [];

    /**
     * Getter for handlers list.
     *
     * @return string[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * Add a new handler to the stack.
     *
     * @param string $className must be a FQCN which implemented IHandler interface
     *
     * @throws Exception if given class does not implementing IHandler
     */
    public function addHandler(string $className): void
    {
        if (in_array($className, $this->handlers)) {
            return;
        }
        if (!is_a($className, IHandler::class, true)) {
            throw new Exception('given handler does not implementing '.IHandler::class);
        }
        $this->handlers[] = $className;
    }

    /**
     * Remove a handler from the list.
     *
     * @param string $className must be FQCN
     */
    public function removeHandler(string $className): void
    {
        $index = array_search($className, $this->handlers);
        if (false === $index) {
            return;
        }
        array_splice($this->handlers, $index, 1);
    }
}
