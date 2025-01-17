<?php

namespace app\models\forms\TaskFavorite;

use app\dto\TaskFavorite\TaskFavoriteChangePositionDto;
use app\kernel\common\models\Form\Form;
use app\models\TaskFavorite;

/**
 * This is the form class for table "task_favorite_change_position".
 *
 * @property ?int $prev_id
 * @property ?int $next_id
 */
class TaskFavoriteChangePositionForm extends Form
{
	public $prev_id;
	public $next_id;

	public function rules(): array
	{
		return [
			[['prev_id', 'next_id'], 'integer'],
			[['prev_id'], 'compare', 'compareAttribute' => 'next_id', '!='],
			[['prev_id'], 'exist', 'targetClass' => TaskFavorite::class, 'targetAttribute' => ['prev_id' => 'id']],
			[['next_id'], 'exist', 'targetClass' => TaskFavorite::class, 'targetAttribute' => ['next_id' => 'id']],
		];
	}

	public function getDto(): TaskFavoriteChangePositionDto
	{
		return new TaskFavoriteChangePositionDto([
			'prev_id' => $this->prev_id,
			'next_id' => $this->next_id,
		]);
	}
}