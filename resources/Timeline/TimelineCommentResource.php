<?php

namespace app\resources\Timeline;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\TimelineActionComment;

class TimelineCommentResource extends JsonResource
{
	private TimelineActionComment $resource;

	public function __construct(TimelineActionComment $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                   => $this->resource->id,
			'timeline_id'          => $this->resource->timeline_id,
			'timeline_step_id'     => $this->resource->timeline_step_id,
			'timeline_step_number' => $this->resource->timeline_step_number,
			'type'                 => $this->resource->type,
			'title'                => $this->resource->title,
			'comment'              => $this->resource->comment,
			'letter_id'            => $this->resource->letter_id,
			'created_at'           => $this->resource->created_at
		];
	}
}