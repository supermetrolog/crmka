<?php

declare(strict_types=1);

namespace app\resources\EntityMessageLink;

use app\enum\EntityMessageLink\EntityMessageLinkKindEnum;
use app\kernel\web\http\resources\JsonResource;
use app\models\EntityMessageLink;
use app\resources\ChatMemberMessage\ChatMemberMessageInlineResource;

class EntityMessageLinkResource extends JsonResource
{
	private const MODEL_BY_KIND = [
		EntityMessageLinkKindEnum::NOTE    => EntityMessageLinkNoteResource::class,
		EntityMessageLinkKindEnum::PIN     => EntityMessageLinkPinResource::class,
		EntityMessageLinkKindEnum::COMMENT => EntityMessageLinkCommentResource::class
	];
	
	private EntityMessageLink $resource;

	public function __construct(EntityMessageLink $resource)
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
			'kind'                   => $this->resource->kind,
			'created_by_id'          => $this->resource->created_by_id,
			'created_at'             => $this->resource->created_at,
			'updated_at'             => $this->resource->updated_at,
			'deleted_at'             => $this->resource->deleted_at,

			'message' => $this->getMessage()->toArray()
		];
	}

	private function getMessage()
	{
		$resource = self::MODEL_BY_KIND[$this->resource->kind] ?? ChatMemberMessageInlineResource::class;

		return new $resource($this->resource->chatMemberMessage);
	}
}