<?php

declare(strict_types=1);

namespace app\resources\ChatMember;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMemberMessage;
use app\resources\Contact\ContactShortResource;
use app\resources\TaskResource;

class ChatMemberMessageResource extends JsonResource
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
			'created_at'          => $this->resource->created_at,
			'updated_at'          => $this->resource->updated_at,
			'from'                => ChatMemberShortResource::make($this->resource->fromChatMember)->toArray(),
			'tasks'               => TaskResource::collection($this->resource->tasks),
			'contacts'            => ContactShortResource::collection($this->resource->contacts),
		];
	}
}