<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Notification\UserNotification;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class UserNotificationResource extends JsonResource
{
	private UserNotification $resource;

	public function __construct(UserNotification $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'mailing_id'  => $this->resource->mailing_id,
			'user_id'     => $this->resource->user_id,
			'subject'     => $this->resource->getSubject(),
			'message'     => $this->resource->getMessage(),
			'notified_at' => $this->resource->notified_at,
			'viewed_at'   => $this->resource->viewed_at,
			'created_at'  => $this->resource->created_at,
			'updated_at'  => $this->resource->updated_at,
			'user'        => UserShortResource::make($this->resource->user)->toArray(),
		];
	}
}