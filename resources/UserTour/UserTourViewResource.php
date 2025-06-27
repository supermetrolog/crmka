<?php

declare(strict_types=1);

namespace app\resources\UserTour;

use app\kernel\web\http\resources\JsonResource;
use app\models\UserTourView;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class UserTourViewResource extends JsonResource
{
	private UserTourView $resource;

	public function __construct(UserTourView $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'           => $this->resource->id,
			'tour_id'      => $this->resource->tour_id,
			'user_id'      => $this->resource->user_id,
			'steps_viewed' => $this->resource->steps_viewed,
			'steps_total'  => $this->resource->steps_total,
			'created_at'   => $this->resource->created_at,

			'user' => UserShortResource::make($this->resource->user)->toArray(),
		];
	}
}