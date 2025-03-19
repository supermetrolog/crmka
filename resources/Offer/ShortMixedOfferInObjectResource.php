<?php

declare(strict_types=1);

namespace app\resources\Offer;

use app\kernel\web\http\resources\JsonResource;
use app\models\OfferMix;

class ShortMixedOfferInObjectResource extends JsonResource
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
			'visual_id'  => $this->resource->visual_id,
			'deal_type'  => $this->resource->deal_type,
			'status'     => $this->resource->status,
			'is_deleted' => $this->resource->isDeleted()
		];
	}
}