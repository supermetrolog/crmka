<?php

declare(strict_types=1);

namespace app\resources\ChatMemberMessage;

use app\kernel\web\http\resources\JsonResource;
use app\models\ChatMemberMessage;
use app\resources\AlertResource;
use app\resources\ChatMember\ChatMemberShortResource;
use app\resources\Contact\ContactShortResource;
use app\resources\Media\MediaResource;
use app\resources\ReminderResource;
use app\resources\Task\TaskResource;
use app\resources\UserNotificationResource;

class ChatMemberMessageSearchResource extends JsonResource
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
			'reply_to_id'         => $this->resource->reply_to_id,
			'message'             => $this->resource->message,
			'template'            => $this->resource->template,
			'created_at'          => $this->resource->created_at,
			'updated_at'          => $this->resource->updated_at,
			'deleted_at'          => $this->resource->deleted_at,
			'is_viewed'           => $this->resource->is_viewed,
			'is_system'           => $this->resource->isSystem(),

			'from'          => ChatMemberShortResource::tryMakeArray($this->resource->fromChatMember),
			'tasks'         => TaskResource::collection($this->resource->tasks),
			'alerts'        => AlertResource::collection($this->resource->alerts),
			'reminders'     => ReminderResource::collection($this->resource->reminders),
			'contacts'      => ContactShortResource::collection($this->resource->contacts),
			'notifications' => UserNotificationResource::collection($this->resource->notifications),
			'tags'          => ChatMemberMessageTagResource::collection($this->resource->tags),
			'files'         => MediaResource::collection($this->resource->files),
			'reply_to'      => ChatMemberMessageShortResource::tryMakeArray($this->resource->replyTo),
			'surveys'       => ChatMemberMessageSurveyResource::collection($this->resource->surveys),
			'to'            => ChatMemberShortResource::tryMakeArray($this->resource->toChatMember)
		];
	}
}