<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

use app\kernel\web\http\resources\JsonResource;
use app\models\Objects;
use app\resources\Offer\ShortMixedOfferInObjectResource;

class ObjectShortResource extends JsonResource
{
	private Objects $resource;

	public function __construct(Objects $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'           => $this->resource->id,
			'address'      => $this->resource->address,
			'complex_id'   => $this->resource->complex_id,
			'contact_id'   => $this->resource->contact_id,
			'agent_id'     => $this->resource->agent_id,
			'consultant'   => UserShortResource::tryMakeArray($this->resource->consultant),
			'is_land'      => $this->resource->is_land,
			'object_class' => $this->resource->object_class,
			'test_only'    => $this->resource->test_only,
			'thumb'        => $this->resource->getThumb(),
			'photos'       => $this->resource->getPhotos(),
			'updated_at'   => $this->getUpdatedAt(),
			'created_at'   => $this->resource->publ_time,
			'company'      => CompanyShortResource::tryMakeArray($this->resource->company),
			'offers'       => ShortMixedOfferInObjectResource::collection($this->resource->offers)
		];
	}

	public function getUpdatedAt(): int
	{
		if ($this->resource->last_update > 0) {
			return $this->resource->last_update;
		}

		return $this->resource->publ_time;
	}
}