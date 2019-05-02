<?php
namespace packages\userpanel\Authentication;

use packages\userpanel\User;

interface IHandler {

	/**
	 * Check authentication of user.
	 * Validator can use http fields directly.
	 * 
	 * @return User|null
	 */
	public function check(): ?User;

	/**
	 * Earse all the sign of current-user from memory.
	 * 
	 * @return void
	 */
	public function forget(): void;
}
