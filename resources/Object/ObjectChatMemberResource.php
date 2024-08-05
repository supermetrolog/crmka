<?php

declare(strict_types=1);

namespace app\resources\Object;

use app\kernel\web\http\resources\JsonResource;
use app\models\ObjectChatMember;
use app\models\Objects;
use app\models\Request;

class ObjectChatMemberResource extends JsonResource
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
			'morph'      => $this->resource->morph,
			'object'     => ObjectResource::make($this->resource->object)->toArray()
		];
	}
}