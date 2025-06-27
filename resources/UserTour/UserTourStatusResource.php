<?php

declare(strict_types=1);

namespace app\resources\UserTour;

use app\kernel\web\http\resources\JsonResource;
use app\models\UserTourStatus;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class UserTourStatusResource extends JsonResource
{
	private UserTourStatus $resource;

	public function __construct(UserTourStatus $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'tour_id'    => $this->resource->tour_id,
			'viewed'     => $this->resource->viewed,
			'reset_at'   => $this->resource->reset_at,
			'user_id'    => $this->resource->user_id,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,

			'user' => UserShortResource::make($this->resource->user)->toArray(),
		];
	}
}