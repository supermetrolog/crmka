<?php

declare(strict_types=1);

namespace app\resources\EntityMessageLink;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMemberMessage;
use app\resources\ChatMember\ChatMemberUserResource;
use app\resources\Media\MediaResource;

class EntityMessageLinkPinResource extends JsonResource
{
	private ChatMemberMessage $resource;

	public function __construct(ChatMemberMessage $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                  => $this->resource->id,
			'from_chat_member_id' => $this->resource->from_chat_member_id,
			'to_chat_member_id'   => $this->resource->to_chat_member_id,
			'message'             => $this->resource->message,
			'template'            => $this->resource->template,
			'created_at'          => $this->resource->created_at,
			'updated_at'          => $this->resource->updated_at,
			'deleted_at'          => $this->resource->deleted_at,
			'is_system'           => $this->resource->isSystem(),
			'from'                => ChatMemberUserResource::make($this->resource->fromChatMember)->toArray(),
			'files'               => MediaResource::collection($this->resource->files)
		];
	}
}