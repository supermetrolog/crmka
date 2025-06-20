<?php

declare(strict_types=1);

namespace app\resources\Company\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\Timeline;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class CompanyRequestTimelineResource extends JsonResource
{
	private Timeline $resource;

	public function __construct(Timeline $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'status'     => $this->resource->status,
			'steps'      => CompanyRequestTimelineStepResource::collection($this->resource->timelineSteps),
			'consultant' => UserShortResource::tryMakeArray($this->resource->consultant),
		];
	}
}