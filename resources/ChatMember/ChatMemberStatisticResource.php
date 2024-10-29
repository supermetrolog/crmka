<?php

declare(strict_types=1);

namespace app\resources\ChatMember;

use app\kernel\web\http\resources\JsonResource;
use app\models\views\ChatMemberStatisticView;

class ChatMemberStatisticResource extends JsonResource
{
	private ChatMemberStatisticView $resource;

	public function __construct(ChatMemberStatisticView $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'chat_member_id'            => $this->resource->chat_member_id,
			'unread_task_count'         => $this->resource->unread_task_count,
			'unread_notification_count' => $this->resource->unread_notification_count,
			'unread_message_count'      => $this->resource->unread_message_count,
			'outdated_call_count'       => $this->resource->outdated_call_count,
			'outdated_call_count_all'   => $this->resource->outdated_call_count_all,
		];
	}
}