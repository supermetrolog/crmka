<?php

declare(strict_types=1);

namespace app\resources\UserNotification;

use app\kernel\web\http\resources\JsonResource;
use app\models\Notification\UserNotificationAction;

class UserNotificationActionViewResource extends JsonResource
{
	private UserNotificationAction $resource;

	public function __construct(UserNotificationAction $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                   => $this->resource->id,
			'user_notification_id' => $this->resource->user_notification_id,
			'code'                 => $this->resource->code,
			'type'                 => $this->resource->type,
			'label'                => $this->resource->label,
			'icon'                 => $this->resource->icon,
			'style'                => $this->resource->style,
			'confirmation'         => $this->resource->confirmation,
			'order'                => $this->resource->order,
			'payload'              => $this->resource->getPayloadArray(),
			'expires_at'           => $this->resource->expires_at,
			'created_at'           => $this->resource->created_at,
			'updated_at'           => $this->resource->updated_at,

			'logs' => UserNotificationActionLogViewResource::collection($this->resource->userNotificationActionLogs),
		];
	}
}