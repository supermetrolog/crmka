<?php

declare(strict_types=1);

namespace app\resources\Company\Object;

use app\kernel\web\http\resources\JsonResource;
use app\models\OfferMix;

class CompanyObjectOfferMixResource extends JsonResource
{
	private OfferMix $resource;

	public function __construct(OfferMix $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'        => $this->resource->id,
			'status'    => $this->resource->status,
			'type_id'   => $this->resource->type_id,
			'deal_type' => $this->resource->deal_type,
			'area_min'  => $this->resource->area_min,
			'area_max'  => $this->resource->area_max,
		];
	}
}