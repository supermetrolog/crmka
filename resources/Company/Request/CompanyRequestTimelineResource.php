<?php

declare(strict_types=1);

namespace app\resources\Company\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\Timeline;

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
			'id'    => $this->resource->id,
			'steps' => CompanyRequestTimelineStepResource::collection($this->resource->timelineSteps)
		];
	}
}