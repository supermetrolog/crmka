<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TaskCommentQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "task".
 *
 * @property int     $id
 * @property string  $message
 * @property int     $created_by_id
 * @property int     $task_id
 * @property string  $created_at
 * @property ?string $updated_at
 * @property ?string $deleted_at
 *
 * @property User    $createdBy
 */
class TaskComment extends AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'task_comment';
	}

	public function rules(): array
	{
		return [
			[['message', 'created_by_id', 'task_id'], 'required'],
			[['created_by_id', 'task_id'], 'integer'],
			[['message'], 'string'],
			[['created_at', 'updated_at'], 'safe'],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
			[['created_by_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'            => 'ID',
			'message'       => 'Message',
			'task_id'       => 'Task ID',
			'created_by_id' => 'Created By ID',
			'created_at'    => 'Created At',
			'updated_at'    => 'Updated At'
		];
	}

	public function getCreatedBy(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'created_by_id']);
	}

	public function getTask(): ActiveQuery
	{
		return $this->hasOne(Task::class, ['id' => 'task_id']);
	}

	public static function find(): TaskCommentQuery
	{
		return new TaskCommentQuery(get_called_class());
	}
}
