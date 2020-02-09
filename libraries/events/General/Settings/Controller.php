<?php
namespace packages\userpanel\events\General\Settings;

interface Controller {
	/**
	 * @return (array of Log)
	 */
	public function store(array $inputs): array;
}
