<?php

namespace app\resources\Timeline;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineStepObjectComment;

class TimelineStepObjectCommentResource extends JsonResource
{
	private TimelineStepObjectComment $resource;

	public function __construct(TimelineStepObjectComment $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                      => $this->resource->id,
			'timeline_id'             => $this->resource->timeline_id,
			'timeline_step_id'        => $this->resource->timeline_step_id,
			'timeline_step_object_id' => $this->resource->timeline_step_object_id,
			'comment'                 => $this->resource->comment,
			'object_id'               => $this->resource->object_id,
			'offer_id'                => $this->resource->offer_id,
			'type_id'                 => $this->resource->type_id,
		];
	}
}