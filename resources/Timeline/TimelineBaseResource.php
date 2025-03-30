<?php

namespace app\resources\Timeline;

use app\kernel\web\http\resources\JsonResource;
use app\models\Timeline;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class TimelineBaseResource extends JsonResource
{
	private Timeline $resource;

	public function __construct(Timeline $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'consultant_id' => $this->resource->consultant_id,
			'request_id'    => $this->resource->request_id,
			'status'        => $this->resource->status,
			'updated_at'    => $this->resource->updated_at,
			'created_at'    => $this->resource->created_at,

			'consultant' => UserShortResource::tryMakeArray($this->resource->consultant)
		];
	}
}