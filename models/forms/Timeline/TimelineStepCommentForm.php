<?php

declare(strict_types=1);

namespace app\models\forms\Timeline;

use app\dto\Timeline\TimelineStepCommentDto;
use app\kernel\common\models\Form\Form;
use app\models\letter\Letter;

class TimelineStepCommentForm extends Form
{
	public $type;
	public $letter_id;
	public $comment;
	public $title;

	public function rules(): array
	{
		return [
			[['type', 'letter_id'], 'integer'],
			[['type', 'comment'], 'required'],
			['comment' => 'string'],
			['title', 'string', 'max' => 255],
			[['letter_id'], 'exist', 'targetClass' => Letter::class, 'targetAttribute' => ['letter_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'letter_id' => 'ID письма',
			'type'      => 'Тип комментария',
			'comment'   => 'Текст комментария',
			'title'     => 'Заголовок комментария',
		];
	}

	public function getDto(): TimelineStepCommentDto
	{
		return new TimelineStepCommentDto([
			'letter_id' => $this->letter_id,
			'type'      => $this->type,
			'comment'   => $this->comment,
			'title'     => $this->title,
		]);
	}
}