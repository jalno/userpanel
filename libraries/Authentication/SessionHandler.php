<?php
namespace packages\userpanel\Authentication;

use packages\base\{Session, Exception};
use packages\userpanel\{Authentication, User};

class SessionHandler implements IHandler {

	const PARAM_USER_ID = "userid";
	const PARAM_LOCK = "lock";
	
	/**
	 * Check authentication of user.
	 * Validator can use http fields directly.
	 * 
	 * @return User|null
	 */
	public function check(): ?User {
		if (!$this->getSession() or Session::get(self::PARAM_LOCK)) {
			return null;
		}

		$userid = Session::get(self::PARAM_USER_ID);
		return (new User)->byID($userid);
	}

	/**
	 * Earse all the sign of current-user from memory.
	 * 
	 * @return void
	 */
	public function forget(): void {
		Session::start();
		if (Session::status()) {
			Session::unsetval(self::PARAM_USER_ID);
			Session::unsetval(self::PARAM_LOCK);
		}
	}

	/**
	 * Save user details (currently just user's ID) in session storage.
	 * 
	 * @throws Exception if no user authenticated yet.
	 * @return void
	 */
	public function setSession(): void {
		$user = Authentication::getUser();
		if (!$user) {
			throw new Exception("no user authenticated yet");
		}
		if (!Session::status()) {
			Session::start();
		}
		Session::set(self::PARAM_USER_ID, $user->id);
	}

	/**
	 * Tempelory lock-out the user by saving additional `PARAM_LOCK` flag in session storage.
	 * check() method will return false as long as this flag does exist.
	 * 
	 * @return void
	 */
	public function lock(): void {
		if (!$this->getSession()) {
			return;
		}
		Session::set(self::PARAM_LOCK, true);
	}

	/**
	 * Remove `PARAM_LOCK` flag which created by lock() method and restore user identety back.
	 * 
	 * @return void
	 */
	public function unlock(): void {
		if (!$this->getSession()) {
			return;
		}
		Session::unsetval(self::PARAM_LOCK);
	}

	/**
	 * read saved user id in session storage.
	 * 
	 * @return int|null
	 */
	public function getUserID(): ?int {
		if (!$this->getSession()) {
			return null;
		}
		return Session::get(self::PARAM_USER_ID);
	}

	/**
	 * Ensure that session system is running and there is `PARAM_USER_ID` key saved on it.
	 * 
	 * @return bool true is good!
	 */
	protected function getSession(): bool {
		if (!Session::status()) {
			Session::start();
		}
		if (!Session::status()) {
			return false;
		}
		$userid = Session::get(self::PARAM_USER_ID);
		return $userid >= 0;
	}
}
