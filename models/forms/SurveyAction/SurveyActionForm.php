<?php

declare(strict_types=1);

namespace app\models\forms\SurveyAction;

use app\dto\SurveyAction\CreateSurveyActionDto;
use app\dto\SurveyAction\UpdateSurveyActionDto;
use app\enum\Survey\SurveyActionStatusEnum;
use app\enum\Survey\SurveyActionTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\Form\Form;
use app\models\Survey;
use Exception;

class SurveyActionForm extends Form
{
	public const SCENARIO_CREATE             = 'scenario_create';
	public const SCENARIO_CREATE_WITH_SURVEY = 'scenario_create_with_survey';
	public const SCENARIO_UPDATE             = 'scenario_update';

	public $survey_id;
	public $type;
	public $target_id;
	public $comment;
	public $status;

	public function rules(): array
	{
		return [
			[['survey_id', 'type'], 'required'],
			[['survey_id', 'target_id'], 'integer'],
			[['type'], 'string', 'max' => 32],
			[['type'], EnumValidator::class, 'enumClass' => SurveyActionTypeEnum::class],
			[['status'], 'string', 'max' => 16],
			[['status'], EnumValidator::class, 'enumClass' => SurveyActionStatusEnum::class],
			[['comment'], 'string', 'max' => 1024],
			['survey_id', 'exist', 'targetClass' => Survey::class, 'targetAttribute' => 'id'],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'target_id',
			'comment',
			'status',
		];

		return [
			self::SCENARIO_CREATE             => [...$common, 'survey_id', 'type'],
			self::SCENARIO_CREATE_WITH_SURVEY => [...$common, 'type'],
			self::SCENARIO_UPDATE             => [...$common],
		];
	}

	/**
	 * @return CreateSurveyActionDto|UpdateSurveyActionDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateSurveyActionDto([
					'type'      => $this->type,
					'target_id' => $this->target_id,
					'comment'   => $this->comment,
					'status'    => $this->status,
					'survey'    => Survey::findOne($this->survey_id),
				]);
			case self::SCENARIO_CREATE_WITH_SURVEY:
				return new CreateSurveyActionDto([
					'type'      => $this->type,
					'target_id' => $this->target_id,
					'status'    => $this->status,
					'comment'   => $this->comment,
				]);

			default:
				return new UpdateSurveyActionDto([
					'target_id' => $this->target_id,
					'status'    => $this->status,
					'comment'   => $this->comment,
				]);
		}
	}
}