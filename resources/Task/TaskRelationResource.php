<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\Task;
use app\resources\ChatMember\ChatMemberShortResource;
use app\resources\ChatMemberMessage\ChatMemberMessageShortResource;

class TaskRelationResource extends JsonResource
{
	private Task $resource;

	public function __construct(Task $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'chat_member_message_id' => $this->getChatMemberMessageId(),
			'chat_member_id'         => $this->getChatMemberId(),
			'chat_member'            => ChatMemberShortResource::tryMakeArray($this->resource->chatMember),
			'chat_member_message'    => ChatMemberMessageShortResource::tryMakeArray($this->resource->chatMemberMessage)
		];
	}

	public function getChatMemberMessageId(): ?int
	{
		$chatMemberMessage = $this->resource->chatMemberMessage;

		if ($chatMemberMessage instanceof ChatMemberMessage) {
			return $chatMemberMessage->id;
		}

		return null;
	}

	public function getChatMemberId(): ?int
	{
		$chatMember = $this->resource->chatMember;

		if ($chatMember instanceof ChatMember) {
			return $chatMember->id;
		}

		return null;
	}
}