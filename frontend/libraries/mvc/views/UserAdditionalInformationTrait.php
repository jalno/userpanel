<?php
namespace themes\clipone\views;

use function packages\base\json\encode;
use themes\clipone\users\{AdditionalInformation, AdditionalInformations};

trait UserAdditionalInformationTrait {

	/** @var AdditionalInformations */
	private $additionalInformations;

	public function AdditionalInformations(): AdditionalInformations {
		if (!$this->additionalInformations) {
			$this->additionalInformations = new AdditionalInformations();
		}
		return $this->additionalInformations;
	}

	/**
	 * @return string
	 */
	protected function buildAddintionalInformations(): string {
		$items = $this->AdditionalInformations()->get();
		if (empty($items)) {
			return "";
		}
		$html = '<table class="table table-condensed table-hover">
		<thead>
			<tr>
				<th colspan="2">' . t("userpanel.profile.additional_informations") . '</th>
			</tr>
		</thead>
		<tbody>';

		foreach ($items as $item) {
			$data = $this->generateItemData($item);
			$classes = $item->getClasses();
			$html .= '
			<tr' . ($classes ? ' class="' . $classes . '"' : '') . ($data ? ' ' . $data : '') . '>
				<td>' . $item->getName() . '</td>
				<td>' . $item->getValue() . '</td>
			</tr>';
		}

		$html .= '
			</tbody>
		</table>';

		return $html;
	}
	
	private function generateItemData(AdditionalInformation $item): string {
		$items = array();
		foreach ($item->getData() as $key => $value) {
			$items[] = 'data-' . $key . '="' . (htmlentities(is_array($value) ? encode($value) : $value)) . '"';
		}
		return implode(" ", $items);
	}
}
