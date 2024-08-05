<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

use app\kernel\web\http\resources\JsonResource;
use app\models\ObjectChatMember;
use app\models\Objects;
use app\models\Request;
use app\resources\Object\ObjectResource;

class ObjectChatMemberShortResource extends JsonResource
{
	private ObjectChatMember $resource;

	public function __construct(ObjectChatMember $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'object_id'  => $this->resource->object_id,
			'type'       => $this->resource->type,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'object'     => ObjectShortResource::make($this->resource->object)->toArray()
		];
	}
}