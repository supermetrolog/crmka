<?php

declare(strict_types=1);

namespace app\resources\UserNotification;

use app\kernel\web\http\resources\JsonResource;
use app\models\Notification\UserNotificationActionLog;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class UserNotificationActionLogSearchResource extends JsonResource
{
	private UserNotificationActionLog $resource;

	public function __construct(UserNotificationActionLog $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                   => $this->resource->id,
			'user_notification_id' => $this->resource->user_notification_id,
			'action_id'            => $this->resource->action_id,
			'user_id'              => $this->resource->user_id,
			'executed_at'          => $this->resource->executed_at,
			'user'                 => UserShortResource::tryMakeArray($this->resource->user),
			'notification'         => UserNotificationSearchResource::tryMakeArray($this->resource->userNotification),
			'action'               => UserNotificationActionBaseResource::tryMakeArray($this->resource->userNotificationAction),
		];
	}
}