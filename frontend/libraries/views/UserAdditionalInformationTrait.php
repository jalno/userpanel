<?php

namespace themes\clipone\Views;

use themes\clipone\Users\ActionButton;
use themes\clipone\Users\AdditionalInformation;
use themes\clipone\Users\AdditionalInformations;

trait UserAdditionalInformationTrait
{
    /** @var AdditionalInformations */
    private $additionalInformations;

    public function AdditionalInformations(): AdditionalInformations
    {
        if (!$this->additionalInformations) {
            $this->additionalInformations = new AdditionalInformations($this);
        }

        return $this->additionalInformations;
    }

    public function buildActionButtons(): string
    {
        $items = $this->AdditionalInformations()->get();
        if (empty($items)) {
            return '';
        }

        $html = '';

        foreach ($items as $item) {
            if ($item instanceof ActionButton) {
                $html .= $item->getHtml();
            }
        }

        return $html;
    }

    protected function buildAddintionalInformations(): string
    {
        $items = array_filter($this->AdditionalInformations()->get(), fn (AdditionalInformation $item) => !($item instanceof ActionButton));

        if (empty($items)) {
            return '';
        }

        $html = '<table class="table table-condensed table-hover">
		<thead>
			<tr>
				<th colspan="2">'.t('userpanel.profile.additional_informations').'</th>
			</tr>
		</thead>
		<tbody>';

        foreach ($items as $item) {
            $html .= $item->getHtml();
        }

        $html .= '
			</tbody>
		</table>';

        return $html;
    }
}
