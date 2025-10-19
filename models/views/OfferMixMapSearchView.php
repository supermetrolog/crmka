<?php

namespace app\models\views;

use app\models\OfferMix;

class OfferMixMapSearchView extends OfferMix
{
	public ?int $offer_state = null;

	public function fields(): array
	{
		$fields = parent::fields();

		$fields['offer_state'] = fn() => $this->offer_state;

		return $fields;
	}
}
