<?php

declare(strict_types=1);

namespace app\resources\UserNotification;

use app\kernel\web\http\resources\JsonResource;
use app\models\Notification\UserNotification;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class UserNotificationViewResource extends JsonResource
{
	private UserNotification $resource;

	public function __construct(UserNotification $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'           => $this->resource->id,
			'mailing_id'   => $this->resource->mailing_id,
			'user_id'      => $this->resource->user_id,
			'templated_id' => $this->resource->template_id,
			'expires_at'   => $this->resource->expires_at,
			'notified_at'  => $this->resource->notified_at,
			'acted_at'     => $this->resource->acted_at,
			'viewed_at'    => $this->resource->viewed_at,
			'created_at'   => $this->resource->created_at,
			'updated_at'   => $this->resource->updated_at,

			'subject'   => $this->resource->getSubject(),
			'message'   => $this->resource->getMessage(),
			'createdBy' => UserShortResource::tryMakeArray($this->resource->getCreatedByUser()),

			'template'  => UserNotificationTemplateViewResource::tryMakeArray($this->resource->getTemplate()),
			'actions'   => UserNotificationActionViewResource::collection($this->resource->getActions()),
			'relations' => UserNotificationRelationViewResource::collection($this->resource->getRelations())
		];
	}
}