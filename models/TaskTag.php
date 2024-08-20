<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TaskTagQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "task_tag".
 *
 * @property int              $id
 * @property string           $name
 * @property string|null      $description
 * @property string           $color
 * @property string           $created_at
 * @property string           $updated_at
 * @property string|null      $deleted_at
 *
 * @property-read ActiveQuery $taskTaskTags
 * @property Task[]           $tasks
 */
class TaskTag extends AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'task_tag';
	}

	public function rules(): array
	{
		return [
			[['name', 'color'], 'required'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['name', 'description'], 'string', 'max' => 255],
			[['color'], 'string', 'max' => 6],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'          => 'ID',
			'name'        => 'Name',
			'description' => 'Description',
			'color'       => 'Color',
			'created_at'  => 'Created At',
			'updated_at'  => 'Updated At',
			'deleted_at'  => 'Deleted At',
		];
	}

	public function getTaskTaskTags(): ActiveQuery
	{
		return $this->hasMany(TaskTaskTag::className(), ['task_tag_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getTasks(): ActiveQuery
	{
		return $this->hasMany(Task::className(), ['id' => 'task_id'])->via('taskTaskTags');
	}

	public static function find(): TaskTagQuery
	{
		return new TaskTagQuery(get_called_class());
	}
}
