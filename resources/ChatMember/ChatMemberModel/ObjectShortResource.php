<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

use app\kernel\web\http\resources\JsonResource;
use app\models\Objects;
use app\models\Request;

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
			'is_land'      => $this->resource->is_land,
			'object_class' => $this->resource->object_class,
			'test_only'    => $this->resource->test_only,
			'thumb'        => $this->resource->getThumb(),
			'updated_at'   => $this->resource->last_update,
			'company'      => CompanyShortResource::tryMakeArray($this->resource->company),
		];
	}
}