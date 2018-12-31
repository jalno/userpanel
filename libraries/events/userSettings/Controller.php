<?php
namespace packages\userpanel\events\settings;
use packages\userpanel\user;

interface Controller {
	/**
	 * @return (array of Log)
	 */
	public function store(array $inputs, user $user): array;
}
