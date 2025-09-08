<?php

declare(strict_types=1);

namespace app\resources\UserNotification;

use app\kernel\web\http\resources\JsonResource;
use app\models\Notification\UserNotificationTemplate;

class UserNotificationTemplateViewResource extends JsonResource
{
	private UserNotificationTemplate $resource;

	public function __construct(UserNotificationTemplate $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'kind'       => $this->resource->kind,
			'priority'   => $this->resource->priority,
			'category'   => $this->resource->category,
			'is_active'  => $this->resource->is_active,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at
		];
	}
}