<?php

declare(strict_types=1);

namespace app\resources\Company\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineStep;
use app\resources\Timeline\TimelineStepFeedbackWayResource;

class CompanyRequestTimelineStepResource extends JsonResource
{
	private TimelineStep $resource;

	public function __construct(TimelineStep $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'status'        => $this->resource->status,
			'done'          => $this->resource->done,
			'negative'      => $this->resource->negative,
			'additional'    => $this->resource->additional,
			'comment'       => $this->resource->comment,
			'date'          => $this->resource->date,
			'objects'       => CompanyRequestTimelineStepObjectResource::collection($this->resource->getUniqueObjects()),
			'feedback_ways' => TimelineStepFeedbackWayResource::collection($this->resource->timelineStepFeedbackways)
		];
	}
}