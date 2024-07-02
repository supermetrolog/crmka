<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Equipment;
use app\resources\ChatMember\ChatMemberModel\CompanyShortResource;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Contact\ContactShortResource;

class EquipmentResource extends JsonResource
{
	private Equipment $resource;

	public function __construct(Equipment $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'              => $this->resource->id,
			'name'            => $this->resource->name,
			'address'         => $this->resource->address,
			'description'     => $this->resource->description,
			'company'         => CompanyShortResource::make($this->resource->company)->toArray(),
			'contact'         => ContactShortResource::make($this->resource->contact)->toArray(),
			'consultant'      => UserShortResource::make($this->resource->consultant)->toArray(),
			'preview'         => MediaResource::make($this->resource->preview)->toArray(),
			'category'        => $this->resource->category,
			'availability'    => $this->resource->availability,
			'delivery'        => $this->resource->delivery,
			'deliveryPrice'   => $this->resource->deliveryPrice,
			'price'           => $this->resource->price,
			'benefit'         => $this->resource->benefit,
			'tax'             => $this->resource->tax,
			'count'           => $this->resource->count,
			'state'           => $this->resource->state,
			'status'          => $this->resource->status,
			'passive_type'    => $this->resource->passive_type,
			'passive_comment' => $this->resource->passive_comment,
			'archived_at'     => $this->resource->archived_at,
			'created_by_type' => $this->resource->created_by_type,
			'created_by_id'   => $this->resource->created_by_id,
			'created_at'      => $this->resource->created_at,
			'updated_at'      => $this->resource->updated_at,
			'deleted_at'      => $this->resource->deleted_at,
		];
	}
}
