<?php

declare(strict_types=1);

namespace app\models\forms\QuestionAnswer;

use app\dto\QuestionAnswer\CreateQuestionAnswerDto;
use app\dto\QuestionAnswer\UpdateQuestionAnswerDto;
use app\kernel\common\models\Form\Form;
use app\models\Field;
use app\models\QuestionAnswer;
use Exception;

class QuestionAnswerForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $field_id;
	public $category;
	public $value;

	public function rules(): array
	{
		return [
			[['field_id', 'category'], 'required'],
			[['field_id', 'category'], 'integer'],
			['category', 'in', 'range' => QuestionAnswer::getCategories()],
			[['value'], 'safe'],
			[['value'], 'string', 'max' => 255],
			[['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'field_id',
			'category',
			'value',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
			self::SCENARIO_UPDATE => [...$common],
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
					'field'    => Field::find()->byId($this->field_id)->one(),
					'category' => $this->category,
					'value'    => $this->value,
				]);

			default:
				return new UpdateQuestionAnswerDto([
					'field'    => Field::find()->byId($this->field_id)->one(),
					'category' => $this->category,
					'value'    => $this->value,
				]);
		}
	}
}