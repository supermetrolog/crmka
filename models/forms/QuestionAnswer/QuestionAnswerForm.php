<?php

declare(strict_types=1);

namespace app\models\forms\QuestionAnswer;

use app\dto\QuestionAnswer\CreateQuestionAnswerDto;
use app\dto\QuestionAnswer\UpdateQuestionAnswerDto;
use app\kernel\common\models\Form\Form;
use app\models\Effect;
use app\models\Field;
use app\models\Question;
use app\models\QuestionAnswer;
use Exception;

class QuestionAnswerForm extends Form
{
	public const SCENARIO_CREATE               = 'scenario_create';
	public const SCENARIO_CREATE_WITH_QUESTION = 'scenario_create_with_question';
	public const SCENARIO_UPDATE               = 'scenario_update';

	public $question_id;
	public $field_id;
	public $category;
	public $value;
	public $effect_ids;

	public function rules(): array
	{
		return [
			[['question_id', 'field_id', 'category'], 'required'],
			[['question_id', 'field_id'], 'integer'],
			['category', 'in', 'range' => QuestionAnswer::getCategories()],
			[['category', 'value'], 'safe'],
			[['category', 'value'], 'string', 'max' => 255],
			[['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
			[['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
			['effect_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => Effect::class,
				'targetAttribute' => ['effect_ids' => 'id'],
			]]
		];
	}

	public function scenarios(): array
	{
		$common = [
			'field_id',
			'category',
			'value',
			'effect_ids'
		];

		return [
			self::SCENARIO_CREATE               => [...$common, 'question_id'],
			self::SCENARIO_CREATE_WITH_QUESTION => [...$common],
			self::SCENARIO_UPDATE               => [...$common, 'question_id'],
		];
	}

	/**
	 * @return CreateQuestionAnswerDto|UpdateQuestionAnswerDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateQuestionAnswerDto([
					'question_id' => $this->question_id,
					'field_id'    => $this->field_id,
					'category'    => $this->category,
					'value'       => $this->value,
					'effectIds'   => $this->effect_ids
				]);
			case self::SCENARIO_CREATE_WITH_QUESTION:
				return new CreateQuestionAnswerDto([
					'field_id'  => $this->field_id,
					'category'  => $this->category,
					'value'     => $this->value,
					'effectIds' => $this->effect_ids
				]);

			default:
				return new UpdateQuestionAnswerDto([
					'question_id' => $this->question_id,
					'field_id'    => $this->field_id,
					'category'    => $this->category,
					'value'       => $this->value,
					'effectIds'   => $this->effect_ids
				]);
		}
	}
}