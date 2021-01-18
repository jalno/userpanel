<?php
namespace themes\clipone\users;

use function packages\base\json\encode;

class AdditionalInformations {

	/** @var array<AdditionalInformation> */
	private $items = array();

	/**
	 * Add addintional information with shown priority
	 * 
	 * @param AdditionalInformation $item
	 * @param int $index
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function set(AdditionalInformation $item, int $index): void {
		if ($index < 0) {
			throw new \InvalidArgumentException("index number is invalid");
		}
		array_splice($this->items, $index, 0, [$item]);
	}

	/**
	 * Add addintional information in first
	 * 
	 * @param AdditionalInformation $item
	 * @return void
	 */
	public function prepend(AdditionalInformation $item): void {
		$this->set($item, 0);
	}

	/**
	 * Add addintional information
	 * 
	 * @param AdditionalInformation $item
	 * @return void
	 */
	public function append(AdditionalInformation $item): void {
		$this->items[] = $item;
	}

	/**
	 * Add addintional information
	 * 
	 * @param AdditionalInformation $item
	 * @return void
	 */
	public function add(AdditionalInformation $item): void {
		$this->append($item);
	}

	/**
	 * Get AdditionalInformations
	 * 
	 * @return array<AdditionalInformation>|array
	 */
	public function get() {
		return $this->items;
	}
}
