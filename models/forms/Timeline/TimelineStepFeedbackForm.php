<?php

declare(strict_types=1);

namespace app\models\forms\Timeline;

use app\dto\Timeline\TimelineStepFeedbackDto;
use app\kernel\common\models\Form\Form;
use app\models\miniModels\TimelineStepFeedbackway;

class TimelineStepFeedbackForm extends Form
{
	public $way;

	public function rules(): array
	{
		return [
			['way', 'integer'],
			['way', 'required'],
			['way', 'in', 'range' => TimelineStepFeedbackway::getWays()],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'way' => 'Тип связи'
		];
	}

	public function getDto(): TimelineStepFeedbackDto
	{
		return new TimelineStepFeedbackDto([
			'way' => $this->way
		]);
	}
}