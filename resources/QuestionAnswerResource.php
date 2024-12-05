<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\QuestionAnswer;

class QuestionAnswerResource extends JsonResource
{
	private QuestionAnswer $resource;

	public function __construct(QuestionAnswer $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'question_id' => $this->resource->question_id,
			'field_id'    => $this->resource->field_id,
			'category'    => $this->resource->category,
			'value'       => $this->resource->value,
			'message'     => $this->resource->message,
			'created_at'  => $this->resource->created_at,
			'updated_at'  => $this->resource->updated_at,
			'deleted_at'  => $this->resource->deleted_at,
			'effects'     => EffectResource::collection($this->resource->effects)
		];
	}
}