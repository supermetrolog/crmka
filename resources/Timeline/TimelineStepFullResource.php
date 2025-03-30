<?php

namespace app\resources\Timeline;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineStep;

class TimelineStepFullResource extends JsonResource
{
	private TimelineStep $resource;

	public function __construct(TimelineStep $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'timeline_id' => $this->resource->timeline_id,
			'number'      => $this->resource->number,
			'comment'     => $this->resource->comment,
			'status'      => $this->resource->status,
			'done'        => $this->resource->done,
			'negative'    => $this->resource->negative,
			'additional'  => $this->resource->additional,
			'date'        => $this->resource->date,
			'updated_at'  => $this->resource->updated_at,
			'created_at'  => $this->resource->created_at,

			'objects'       => TimelineStepObjectFullResource::collection($this->resource->getUniqueObjects()),
			'feedback_ways' => TimelineStepFeedbackWayResource::collection($this->resource->timelineStepFeedbackways),
			'comments'      => TimelineCommentResource::collection($this->resource->timelineActionComments)
		];
	}
}