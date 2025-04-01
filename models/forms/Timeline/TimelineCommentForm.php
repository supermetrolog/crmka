<?php

declare(strict_types=1);

namespace app\models\forms\Timeline;

use app\dto\Timeline\TimelineCommentDto;
use app\kernel\common\models\Form\Form;
use app\models\letter\Letter;
use app\models\miniModels\TimelineStep;

class TimelineCommentForm extends Form
{
	public $timeline_step_id;
	public $type;
	public $letter_id;
	public $comment;
	public $title;

	public function rules(): array
	{
		return [
			[['timeline_step_id', 'type', 'letter_id'], 'integer'],
			[['timeline_step_id', 'type', 'comment'], 'required'],
			['comment' => 'string'],
			['title' => 'string', 'max' => 255],
			[['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStep::class, 'targetAttribute' => ['timeline_step_id' => 'id']],
			[['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::class, 'targetAttribute' => ['letter_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'timeline_step_id' => 'ID шага таймлайна',
			'letter_id'        => 'ID письма',
			'type'             => 'Тип комментария',
			'comment'          => 'Текст комментария',
			'title'            => 'Заголовок комментария',
		];
	}

	public function getDto(): TimelineCommentDto
	{
		return new TimelineCommentDto([
			'timelineStep' => TimelineStep::findOne($this->timeline_step_id),
			'letter_id'    => $this->letter_id,
			'type'         => $this->type,
			'comment'      => $this->comment,
			'title'        => $this->title,
		]);
	}
}