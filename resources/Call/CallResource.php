<?php

declare(strict_types=1);

namespace app\resources\Call;

use app\kernel\web\http\resources\JsonResource;
use app\models\Call;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Contact\ContactShortResource;

class CallResource extends JsonResource
{
	private Call $resource;

	public function __construct(Call $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'user_id'     => $this->resource->user_id,
			'type'        => $this->resource->type,
			'status'      => $this->resource->status,
			'description' => $this->resource->description,
			'contact_id'  => $this->resource->contact_id,
			'phone_id'    => $this->resource->phone_id,
			'created_at'  => $this->resource->created_at,
			'updated_at'  => $this->resource->updated_at,
			'deleted_at'  => $this->resource->deleted_at,

			'user'    => UserShortResource::make($this->resource->user)->toArray(),
			'contact' => ContactShortResource::make($this->resource->contact)->toArray(),
		];
	}
}