<?php

declare(strict_types=1);

namespace app\resources\EntityPinnedMessage;

use app\kernel\web\http\resources\JsonResource;
use app\models\EntityPinnedMessage;
use app\resources\ChatMemberMessage\ChatMemberMessageInlineResource;

class EntityPinnedMessageResource extends JsonResource
{
	private EntityPinnedMessage $resource;

	public function __construct(EntityPinnedMessage $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                     => $this->resource->id,
			'entity_id'              => $this->resource->entity_id,
			'entity_type'            => $this->resource->entity_type,
			'chat_member_message_id' => $this->resource->chat_member_message_id,
			'created_by_id'          => $this->resource->created_by_id,
			'created_at'             => $this->resource->created_at,
			'updated_at'             => $this->resource->updated_at,
			'deleted_at'             => $this->resource->deleted_at,

			'message' => ChatMemberMessageInlineResource::make($this->resource->chatMemberMessage)->toArray(),
		];
	}
}