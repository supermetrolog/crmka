<?php

declare(strict_types=1);

namespace app\resources\UserNotification;

use app\kernel\web\http\resources\JsonResource;
use app\models\Notification\UserNotificationRelation;

class UserNotificationRelationViewResource extends JsonResource
{
	private UserNotificationRelation $resource;

	public function __construct(UserNotificationRelation $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'              => $this->resource->id,
			'notification_id' => $this->resource->notification_id,
			'entity_id'       => $this->resource->entity_id,
			'entity_type'     => $this->resource->entity_type,
			'created_at'      => $this->resource->created_at,
			'updated_at'      => $this->resource->updated_at,
		];
	}
}