<?php

declare(strict_types=1);

namespace app\resources\Company\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineStep;

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
			'id'     => $this->resource->id,
			'status' => $this->resource->status
		];
	}
}