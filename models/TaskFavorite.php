<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TaskFavoriteQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\ActiveQuery\UserQuery;

/**
 * This is the model class for table "task_favorite".
 *
 * @property int     $id
 * @property int     $task_id
 * @property int     $user_id
 * @property ?int    $prev_id
 * @property string  $created_at
 * @property ?string $deleted_at
 *
 * @property Task    $task
 * @property User    $user
 */
class TaskFavorite extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'task_favorite';
	}

	public function rules(): array
	{
		return [
			[['task_id', 'user_id'], 'required'],
			[['task_id', 'user_id', 'prev_id'], 'integer'],
			[['created_at', 'deleted_at'], 'safe'],
			[['task_id'], 'exist', 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['prev_id'], 'exist', 'targetClass' => TaskFavorite::class, 'targetAttribute' => ['prev_id' => 'id']]
		];
	}

	public function getTask(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->hasOne(Task::class, ['id' => 'task_id']);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public static function find(): TaskFavoriteQuery
	{
		return new TaskFavoriteQuery(self::class);
	}
}