<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TaskObserverQuery;
use app\models\User\User;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "task".
 *
 * @property int     $id
 * @property int     $task_id
 * @property int     $user_id
 * @property ?int    $created_by_id
 * @property ?string $viewed_at
 * @property string  $created_at
 * @property string  $updated_at
 *
 * @property ?User   $createdBy
 * @property User    $user
 * @property Task    $task
 */
class TaskObserver extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'task_observer';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'task_id'], 'required'],
			[['user_id', 'created_by_id', 'task_id'], 'integer'],
			[['created_at', 'updated_at', 'viewed_at'], 'safe'],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['created_by_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'            => 'ID',
			'task_id'       => 'Task ID',
			'user_id'       => 'User ID',
			'created_by_id' => 'Created By ID',
			'created_at'    => 'Created At',
			'updated_at'    => 'Updated At',
			'viewed_at'     => 'Viewed At',
		];
	}

	public function getCreatedBy(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getTask(): ActiveQuery
	{
		return $this->hasOne(Task::class, ['id' => 'task_id']);
	}

	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public static function find(): TaskObserverQuery
	{
		return new TaskObserverQuery(get_called_class());
	}

	/**
	 * Return true if the observer viewed the task, otherwise false
	 *
	 * @return bool
	 */
	public function isViewed(): bool
	{
		return $this->viewed_at === null;
	}

	/**
	 * Return true if the observer not viewed the task, otherwise false
	 *
	 * @return bool
	 */
	public function isNotViewed(): bool
	{
		return !$this->isViewed();
	}
}
