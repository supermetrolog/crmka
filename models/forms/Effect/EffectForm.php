<?php

declare(strict_types=1);

namespace app\models\forms\Effect;

use app\dto\Effect\CreateEffectDto;
use app\dto\Effect\UpdateEffectDto;
use app\kernel\common\models\Form\Form;
use app\models\Effect;

class EffectForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $title;
	public $description;
	public $kind;

	public function rules(): array
	{
		return [
			[['title', 'kind'], 'required'],
			[['title', 'kind'], 'string', 'max' => 64],
			[['kind'], 'unique', 'targetClass' => Effect::class],
			[['description'], 'string', 'max' => 255],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'title',
			'description'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'kind'],
			self::SCENARIO_UPDATE => [...$common],
		];
	}

	public function getDto()
	{
		switch ($this->getScenario()) {
			case self::SCENARIO_CREATE:
				return new CreateEffectDto([
					'title'       => $this->title,
					'kind'        => $this->kind,
					'description' => $this->description
				]);
			default:
				return new UpdateEffectDto([
					'title'       => $this->title,
					'description' => $this->description
				]);
		}
	}
}