<?php

namespace packages\userpanel\Authentication;

use packages\base\Exception;
use packages\base\Json;
use packages\base\Session;
use packages\userpanel\Authentication;
use packages\userpanel\User;

class SessionHandler implements IHandler
{
    public const PARAM_USER_ID = 'userid';
    public const PARAM_LOCK = 'lock';
    public const PARAM_PREVIOUS_USERS = 'previoususers';

    /**
     * Check authentication of user.
     * Validator can use http fields directly.
     */
    public function check(): ?User
    {
        if (!$this->getSession() or Session::get(self::PARAM_LOCK)) {
            return null;
        }

        $userid = Session::get(self::PARAM_USER_ID);

        return (new User())->byID($userid);
    }

    /**
     * Earse all the sign of current-user from memory.
     */
    public function forget(): void
    {
        if (!$this->getSession()) {
            return;
        }
        Session::unset(self::PARAM_USER_ID);
        Session::unset(self::PARAM_LOCK);
    }

    /**
     * Save user details (currently just user's ID) in session storage.
     *
     * @throws Exception                     if no user authenticated yet
     * @throws Session\StartSessionException if cannot start the session
     */
    public function setSession(): void
    {
        $user = Authentication::getUser();
        if (!$user) {
            throw new Exception('no user authenticated yet');
        }
        Session::start();
        Session::set(self::PARAM_USER_ID, $user->id);
    }

    /**
     * Tempelory lock-out the user by saving additional `PARAM_LOCK` flag in session storage.
     * check() method will return false as long as this flag does exist.
     */
    public function lock(): void
    {
        if (!$this->getSession()) {
            return;
        }
        Session::set(self::PARAM_LOCK, true);
    }

    /**
     * Remove `PARAM_LOCK` flag which created by lock() method and restore user identety back.
     */
    public function unlock(): void
    {
        if (!$this->getSession()) {
            return;
        }
        Session::unset(self::PARAM_LOCK);
    }

    /**
     * read saved user id in session storage.
     */
    public function getUserID(): ?int
    {
        if (!$this->getSession()) {
            return null;
        }

        return Session::get(self::PARAM_USER_ID);
    }

    public function addPreviousUser(User $prevUser): void
    {
        if (!$this->getSession()) {
            return;
        }
        $prevUsers = $this->getPreviousUsers();
        $prevUsers[] = $prevUser->id;
        Session::set(self::PARAM_PREVIOUS_USERS, $prevUsers);
    }

    public function getPreviousUsers(): array
    {
        Session::start();
        $prevUsers = Session::get(self::PARAM_PREVIOUS_USERS) ?? [];
        if (is_string($prevUsers)) { // for backward campatibility: previous users was saved in json format
            $prevUsers = Json\Decode($prevUsers);
        }

        return $prevUsers;
    }

    public function popPreviousUser(): ?User
    {
        Session::start();
        $prevUsers = $this->getPreviousUsers();
        if (!$prevUsers) {
            return null;
        }
        $lastUserId = array_pop($prevUsers);
        if (empty($prevUsers)) {
            Session::unset(self::PARAM_PREVIOUS_USERS);
        } else {
            Session::set(self::PARAM_PREVIOUS_USERS, $prevUsers);
        }

        return (new User())->byId($lastUserId);
    }

    public function clearPreviousUsers(): void
    {
        Session::start();
        Session::unset(self::PARAM_PREVIOUS_USERS);
    }

    /**
     * Ensure that session system is running and there is `PARAM_USER_ID` key saved on it.
     *
     * @return bool true is good!
     *
     * @throws Session\StartSessionException if cannot start session
     */
    protected function getSession(): bool
    {
        Session::start();
        $userid = Session::get(self::PARAM_USER_ID);

        return $userid > 0;
    }
}
