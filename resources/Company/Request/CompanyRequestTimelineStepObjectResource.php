<?php

declare(strict_types=1);

namespace app\resources\Company\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineStepObject;

class CompanyRequestTimelineStepObjectResource extends JsonResource
{
	private TimelineStepObject $resource;

	public function __construct(TimelineStepObject $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'status'     => $this->resource->status,
			'comment'    => $this->resource->comment,
			'object_id'  => $this->resource->object_id,
			'offer_id'   => $this->resource->offer_id,
			'type_id'    => $this->resource->type_id,
			'complex_id' => $this->resource->complex_id,
			'created_at' => $this->resource->created_at,
		];
	}
}