<?php

declare(strict_types=1);

namespace app\resources\ChatMemberMessage;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMemberMessage;
use app\resources\ChatMember\ChatMemberShortResource;
use app\resources\Contact\ContactShortResource;
use app\resources\Media\MediaResource;

class ChatMemberMessageShortResource extends JsonResource
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
			'from'                => ChatMemberShortResource::make($this->resource->fromChatMember)->toArray(),
			'contacts'            => ContactShortResource::collection($this->resource->contacts),
			'tags'                => ChatMemberMessageTagResource::collection($this->resource->tags),
			'files'               => MediaResource::collection($this->resource->files),
			'surveys'             => ChatMemberMessageSurveyResource::collection($this->resource->surveys)
		];
	}
}