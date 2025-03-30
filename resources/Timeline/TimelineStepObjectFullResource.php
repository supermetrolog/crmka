<?php

namespace app\resources\Timeline;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineStepObject;

class TimelineStepObjectFullResource extends JsonResource
{
	private TimelineStepObject $resource;

	public function __construct(TimelineStepObject $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			TimelineStepObjectBaseResource::make($this->resource)->toArray(),
			[
				'duplicate_count' => $this->resource->duplicate_count,
				'comments'        => TimelineStepObjectCommentResource::collection($this->resource->comments),
				'offer'           => $this->resource->offer
			]
		);
	}
}