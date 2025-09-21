<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TaskHistoryQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\User\User;
use yii\db\ActiveQuery;
use yii\helpers\Json;

/**
 * This is the model class for table "task".
 *
 * @property int               $id
 * @property int               $user_id
 * @property ?string           $message
 * @property string            $title
 * @property int               $status
 * @property string|null       $start
 * @property string|null       $end
 * @property string            $created_by_type
 * @property int               $created_by_id
 * @property string            $created_at
 * @property string            $updated_at
 * @property string            $deleted_at
 * @property string|null       $impossible_to
 * @property int               $task_id
 * @property ?int              $prev_id
 * @property ?string           $state
 *
 * @property-read ?mixed       $jsonState
 * @property-read Task         $task
 * @property-read ?TaskHistory $prev
 * @property-read User         $user
 * @property-read User         $createdByUser
 * @property-read User         $createdBy
 * @property-read TaskEvent[]  $events
 */
class TaskHistory extends AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'task_history';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'title', 'status', 'created_by_type', 'created_by_id', 'task_id'], 'required'],
			[['user_id', 'status', 'created_by_id', 'task_id', 'prev_id'], 'integer'],
			[['message', 'state'], 'string'],
			[['title'], 'string', 'max' => 255, 'min' => 16],
			[['start', 'end', 'created_at', 'updated_at', 'impossible_to'], 'safe'],
			[['created_by_type'], 'string', 'max' => 255],
			['status', 'in', 'range' => Task::getStatuses()],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
			[['prev_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskHistory::class, 'targetAttribute' => ['prev_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'              => 'ID',
			'user_id'         => 'User ID',
			'message'         => 'Message',
			'status'          => 'Status',
			'start'           => 'Start',
			'end'             => 'End',
			'created_by_type' => 'Created By Type',
			'created_by_id'   => 'Created By ID',
			'created_at'      => 'Created At',
			'updated_at'      => 'Updated At',
			'impossible_to'   => 'Impossible To',
			'task_id'         => 'Task ID',
			'prev_id'         => 'Prev ID',
			'state'           => 'State',
		];
	}

	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getCreatedByUser(): ActiveQuery
	{
		return $this->morphBelongTo(User::class, 'id', 'created_by');
	}

	public function getCreatedBy(): AR
	{
		return $this->createdByUser;
	}

	public function getTask(): TaskQuery
	{
		/** @var TaskQuery */
		return $this->hasOne(Task::class, ['id' => 'task_id']);
	}

	public function getPrev(): TaskHistoryQuery
	{
		/** @var TaskHistoryQuery */
		return $this->hasOne(TaskHistory::class, ['id' => 'prev_id']);
	}

	/** @return ?mixed */
	public function getJsonState()
	{
		return Json::decode($this->state);
	}

	public function getEvents(): ActiveQuery
	{
		return $this->hasMany(TaskEvent::class, ['task_history_id' => 'id']);
	}

	public static function find(): TaskHistoryQuery
	{
		return new TaskHistoryQuery(get_called_class());
	}
}
