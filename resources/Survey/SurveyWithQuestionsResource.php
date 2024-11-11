<?php

declare(strict_types=1);

namespace app\resources\Survey;

use app\kernel\web\http\resources\JsonResource;
use app\models\Question;
use app\models\Survey;
use app\resources\Contact\ContactResource;
use app\resources\QuestionAnswerResource;
use app\resources\QuestionResource;
use app\resources\User\UserResource;

class SurveyWithQuestionsResource extends JsonResource
{
	private Survey $resource;

	/**
	 * @var Question[]
	 */
	private array $questions;

	/**
	 * @param Question[] $questions
	 */
	public function __construct(Survey $resource, array $questions = [])
	{
		$this->resource  = $resource;
		$this->questions = $questions;
	}

	public function toArray(): array
	{
		return [
			'id'             => $this->resource->id,
			'user_id'        => $this->resource->user_id,
			'contact_id'     => $this->resource->contact_id,
			'created_at'     => $this->resource->created_at,
			'updated_at'     => $this->resource->updated_at,
			'chat_member_id' => $this->resource->chat_member_id,

			'user'    => UserResource::make($this->resource->user)->toArray(),
			'contact' => ContactResource::make($this->resource->contact)->toArray(),

			'questions' => $this->getQuestions(),
		];
	}

	private function getQuestions(): array
	{
		$questions = [];

		foreach ($this->questions as $question) {
			$answers = [];

			foreach ($question->answers ?? [] as $answer) {
				$buffer                         = QuestionAnswerResource::make($answer)->toArray();
				$buffer['surveyQuestionAnswer'] = SurveyQuestionAnswerResource::tryMakeArray(
					$answer->surveyQuestionAnswer ?? null,
				);

				$answers[$answer->category][] = $buffer;
			}

			$buffer            = QuestionResource::make($question)->toArray();
			$buffer['answers'] = $answers;

			$questions[] = $buffer;
		}

		return $questions;
	}
}