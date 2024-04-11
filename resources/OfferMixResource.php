<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\OfferMix;
use app\models\Request;

class OfferMixResource extends JsonResource
{
	private OfferMix $resource;

	public function __construct(OfferMix $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'company_id' => $this->resource->company_id,
		];
	}
}