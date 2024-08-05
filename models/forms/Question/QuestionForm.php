<?php

declare(strict_types=1);

namespace app\models\forms\Question;

use app\dto\Question\CreateQuestionDto;
use app\dto\Question\UpdateQuestionDto;
use app\kernel\common\models\Form\Form;
use Exception;

class QuestionForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $text;

	public function rules(): array
	{
		return [
			[['text'], 'required'],
			[['text'], 'string', 'max' => 1024],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'text',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
			self::SCENARIO_UPDATE => [...$common],
		];
	}

	/**
	 * @return CreateQuestionDto|UpdateQuestionDto
	 * @throws Exception
	 */
	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateQuestionDto([
					'text' => $this->text,
				]);

			default:
				return new UpdateQuestionDto([
					'text' => $this->text,
				]);
		}
	}
}