<?php

declare(strict_types=1);

namespace app\models\forms\SurveyQuestionAnswer;

use app\dto\SurveyQuestionAnswer\CreateSurveyQuestionAnswerDto;
use app\dto\SurveyQuestionAnswer\UpdateSurveyQuestionAnswerDto;
use app\kernel\common\models\Form\Form;
use app\models\QuestionAnswer;
use app\models\Survey;
use Exception;

class SurveyQuestionAnswerForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $question_answer_id;
	public $survey_id;
	public $value;

	public function rules(): array
	{
		return [
			[['question_answer_id', 'survey_id'], 'required'],
			[['question_answer_id', 'survey_id'], 'integer'],
			[['value'], 'safe'],
			[['value'], 'string', 'max' => 255],
			[['question_answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionAnswer::className(), 'targetAttribute' => ['question_answer_id' => 'id']],
			[['survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::className(), 'targetAttribute' => ['survey_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'question_answer_id',
			'survey_id',
			'value',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
			self::SCENARIO_UPDATE => [...$common],
		];
	}

	/**
	 * @return CreateSurveyQuestionAnswerDto|UpdateSurveyQuestionAnswerDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateSurveyQuestionAnswerDto([
					'question_answer_id' => $this->question_answer_id,
					'survey_id'          => $this->survey_id,
					'value'              => $this->value,
				]);

			default:
				return new UpdateSurveyQuestionAnswerDto([
					'question_answer_id' => $this->question_answer_id,
					'survey_id'          => $this->survey_id,
					'value'              => $this->value,
				]);
		}
	}
}