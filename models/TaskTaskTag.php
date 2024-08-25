<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TaskTaskTagQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "task_task_tag".
 *
 * @property int         $task_id
 * @property int         $task_tag_id
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 *
 * @property Task        $task
 * @property TaskTag     $taskTag
 */
class TaskTaskTag extends AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'task_task_tag';
	}

	public function rules(): array
	{
		return [
			[['task_id', 'task_tag_id'], 'required'],
			[['task_id', 'task_tag_id'], 'integer'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['task_id', 'task_tag_id'], 'unique', 'targetAttribute' => ['task_id', 'task_tag_id']],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
			[['task_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskTag::className(), 'targetAttribute' => ['task_tag_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'task_id'     => 'Task ID',
			'task_tag_id' => 'Task Tag ID',
			'created_at'  => 'Created At',
			'updated_at'  => 'Updated At',
			'deleted_at'  => 'Deleted At',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getTask(): ActiveQuery
	{
		return $this->hasOne(Task::className(), ['id' => 'task_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getTaskTag(): ActiveQuery
	{
		return $this->hasOne(TaskTag::className(), ['id' => 'task_tag_id']);
	}

	public static function find(): TaskTaskTagQuery
	{
		return new TaskTaskTagQuery(get_called_class());
	}
}
