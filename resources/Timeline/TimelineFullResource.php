<?php

namespace app\resources\Timeline;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Timeline;

class TimelineFullResource extends JsonResource
{
	private Timeline $resource;

	public function __construct(Timeline $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			TimelineBaseResource::make($this->resource)->toArray(),
			[
				'comments' => TimelineCommentResource::collection($this->resource->timelineActionComments),
				'steps'    => TimelineStepFullResource::collection($this->resource->timelineSteps)
			]
		);
	}
}