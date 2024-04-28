<?php

namespace packages\userpanel;

use packages\base\Response;
use packages\userpanel\Authentication\IHandler;
use packages\userpanel\Events\AuthenticationHandlersEvent;

class Authentication
{
    /**
     * @var User|null Authenticated User
     */
    protected static $user;

    /**
     * @var IHandler|null authentication handler instance
     */
    protected static $handler;

    /**
     * Setter for authenticated user.
     * this method doesn't set the handler flags.
     */
    public static function setUser(?User $user): void
    {
        self::$user = $user;
    }

    /**
     * Getter for authenticated user.
     */
    public static function getUser(): ?User
    {
        return self::$user;
    }

    /**
     * Get authenticated user's ID.
     */
    public static function getID(): ?int
    {
        return self::$user ? self::$user->id : null;
    }

    /**
     * Get the user's name.
     */
    public static function getName(): ?string
    {
        return self::$user ? self::$user->name : null;
    }

    /**
     * Get the authentication handler.
     */
    public static function getHandler(): ?IHandler
    {
        return self::$handler;
    }

    /**
     * Set handler instance.
     *
     * @param IHandler|null
     */
    public static function setHandler(?IHandler $handler): void
    {
        self::$handler = $handler;
    }

    /**
     * Check user authentication.
     */
    public static function check(): bool
    {
        if (self::$handler) {
            self::$user = self::$handler->check();

            return null !== self::$user;
        }
        $event = new AuthenticationHandlersEvent();
        $event->trigger();
        $handlers = $event->getHandlers();
        foreach ($handlers as $handler) {
            $instance = new $handler();
            self::$user = $instance->check();
            if (self::$user) {
                self::$handler = $instance;

                return true;
            }
        }

        return false;
    }

    /**
     * Forget identity of user and authentication handler.
     */
    public static function forget(): void
    {
        if (self::$handler) {
            self::$handler->forget();
        }
        self::$user = null;
        self::$handler = null;
    }

    /**
     * Response for redirecting non-authenticated user to login form.
     *
     * @deprecated
     */
    public static function FailResponse(): Response
    {
        return (new Controllers\Dashboard())->authError();
    }
}
