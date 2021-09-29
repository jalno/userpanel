<?php
namespace themes\clipone\views;

use packages\userpanel\Authorization;

trait UsertypesTrait {

	public function hasChildrentypeToCopy(): bool {

		$childrenTypes = Authorization::childrenTypes();

		if (empty($childrenTypes)) {
			return false;
		}

		$usertype = $this->getData('usertype');

		if ($usertype) {

			$key = array_search($usertype->id, $childrenTypes);

			if ($key !== false) {
				unset($childrenTypes[$key]);
			}
		}

		return !empty($childrenTypes);
	}

	public function getChildrenUsertypesForSelect() {

		$options = array(
			array(
				"title" => t("select.empty_option"),
				"value" => "",
			)
		);

		$usertype = $this->getData('usertype');
		
		foreach ($this->getChildrenTypes() as $type) {

			if ($usertype and $usertype->id == $type->id) {
				continue;
			}

			$options[] = array(
				"title" => "#{$type->id}-{$type->title}",
				"value" => $type->id,
			);
		}

		return $options;
	}
}
