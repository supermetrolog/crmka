<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\kernel\web\http\resources\JsonResource;
use app\models\SurveyQuestionAnswer;
use app\resources\Media\MediaShortResource;
use app\resources\Task\TaskResource;
use yii\base\Exception;

class SurveyQuestionAnswerResource extends JsonResource
{
	private SurveyQuestionAnswer $resource;

	public function __construct(SurveyQuestionAnswer $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @throws Exception
	 */
	public function toArray(): array
	{
		return [
			'id'                 => $this->resource->id,
			'question_answer_id' => $this->resource->question_answer_id,
			'survey_id'          => $this->resource->survey_id,
			'value'              => $this->resource->toEncodedValue(),

			'files' => MediaShortResource::collection($this->resource->files),
			'tasks' => TaskResource::collection($this->resource->tasks)
		];
	}
}