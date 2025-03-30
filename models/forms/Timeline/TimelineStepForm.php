<?php

declare(strict_types=1);

namespace app\models\forms\Timeline;

use app\dto\Timeline\UpdateTimelineStepDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\Form\Form;
use app\models\miniModels\TimelineStep;
use Exception;

class TimelineStepForm extends Form
{
	public $additional;
	public $comment;
	public $date;
	public $done;
	public $negative;
	public $status;

	public function rules(): array
	{
		return [
			[['status', 'additional', 'negative', 'done'], 'integer'],
			['status', 'in', 'range' => TimelineStep::getStatuses()],
			[['comment'], 'string', 'max' => 255],
			['date', 'safe']
		];
	}

	public function attributeLabels(): array
	{
		return [
			'status'     => 'Статус',
			'additional' => 'Дополнительный флаг',
			'comment'    => 'Комментарий',
			'date'       => 'Дата',
			'done'       => 'Выполнено',
			'negative'   => 'Отрицательно'
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): UpdateTimelineStepDto
	{
		return new UpdateTimelineStepDto([
			'comment'    => $this->comment,
			'status'     => $this->status,
			'additional' => $this->additional,
			'done'       => $this->done,
			'negative'   => $this->negative,
			'date'       => DateTimeHelper::tryMake($this->date)
		]);
	}
}