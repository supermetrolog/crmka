<?php

declare(strict_types=1);

namespace app\resources;

use app\helpers\JSONHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\SurveyQuestionAnswer;
use JsonException;

class SurveyQuestionAnswerResource extends JsonResource
{
	private SurveyQuestionAnswer $resource;

	public function __construct(SurveyQuestionAnswer $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @throws JsonException
	 */
	public function toArray(): array
	{
		return [
			'id'                 => $this->resource->id,
			'question_answer_id' => $this->resource->question_answer_id,
			'survey_id'          => $this->resource->survey_id,
			'value'              => JSONHelper::decode($this->resource->value),
		];
	}
}