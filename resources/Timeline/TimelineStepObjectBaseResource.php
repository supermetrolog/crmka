<?php

namespace app\resources\Timeline;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineStepObject;

class TimelineStepObjectBaseResource extends JsonResource
{
	private TimelineStepObject $resource;

	public function __construct(TimelineStepObject $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'               => $this->resource->id,
			'timeline_id'      => $this->resource->timeline_id,
			'timeline_step_id' => $this->resource->timeline_step_id,
			'object_id'        => $this->resource->object_id,
			'status'           => $this->resource->status,
			'option'           => $this->resource->option,
			'type_id'          => $this->resource->type_id,
			'offer_id'         => $this->resource->offer_id,
			'complex_id'       => $this->resource->complex_id,
			'comment'          => $this->resource->comment,
			'class_name'       => $this->resource->class_name,
			'deal_type_name'   => $this->resource->deal_type_name,
			'visual_id'        => $this->resource->visual_id,
			'address'          => $this->resource->address,
			'area'             => $this->resource->area,
			'price'            => $this->resource->price,
			'image'            => $this->resource->image,
			'created_at'       => $this->resource->created_at,
			'updated_at'       => $this->resource->updated_at
		];
	}
}