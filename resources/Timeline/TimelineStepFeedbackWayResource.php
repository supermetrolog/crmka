<?php

namespace app\resources\Timeline;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineStepFeedbackway;

class TimelineStepFeedbackWayResource extends JsonResource
{
	private TimelineStepFeedbackway $resource;

	public function __construct(TimelineStepFeedbackway $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'               => $this->resource->id,
			'timeline_step_id' => $this->resource->timeline_step_id,
			'way'              => $this->resource->way,
			'created_at'       => $this->resource->created_at,
			'updated_at'       => $this->resource->updated_at
		];
	}
}