<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Question;

class QuestionResource extends JsonResource
{
	private Question $resource;

	public function __construct(Question $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'text'       => $this->resource->text,
			'group'      => $this->resource->group,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'deleted_at' => $this->resource->deleted_at,
		];
	}
}