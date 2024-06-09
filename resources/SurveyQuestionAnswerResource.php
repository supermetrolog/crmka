<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\SurveyQuestionAnswer;

class SurveyQuestionAnswerResource extends JsonResource
{
	private SurveyQuestionAnswer $resource;

	public function __construct(SurveyQuestionAnswer $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                 => $this->resource->id,
			'question_answer_id' => $this->resource->question_answer_id,
			'survey_id'          => $this->resource->survey_id,
			'value'              => $this->resource->value,
		];
	}
}