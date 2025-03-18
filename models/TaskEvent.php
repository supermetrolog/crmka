<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TaskHistoryQuery;

/**
 * This is the model class for table "task_event".
 *
 * @property int         $id
 * @property int         $task_history_id
 * @property string      $event_type
 * @property string      $created_at
 *
 * @property TaskHistory $taskHistory
 */
class TaskEvent extends AR
{
	public const EVENT_TYPE_CREATED               = 'created';
	public const EVENT_TYPE_STATUS_CHANGED        = 'status_changed';
	public const EVENT_TYPE_ASSIGNED              = 'assigned';
	public const EVENT_TYPE_DESCRIPTION_CHANGED   = 'description_changed';
	public const EVENT_TYPE_TITLE_CHANGED         = 'title_changed';
	public const EVENT_TYPE_STARTING_DATE_CHANGED = 'starting_date_changed';
	public const EVENT_TYPE_ENDING_DATE_CHANGED   = 'ending_date_changed';
	public const EVENT_TYPE_TAGS_CHANGED          = 'tags_changed';
	public const EVENT_TYPE_OBSERVERS_CHANGED     = 'observers_changed';
	public const EVENT_TYPE_DELETED               = 'deleted';
	public const EVENT_TYPE_RESTORED              = 'restored';
	public const EVENT_TYPE_OBSERVED              = 'observed';
	public const EVENT_TYPE_FILE_CREATED          = 'file_created';
	public const EVENT_TYPE_FILE_DELETED          = 'file_deleted';
	public const EVENT_TYPE_FILES_CHANGED         = 'files_changed';
	public const EVENT_TYPE_POSTPONED             = 'postponed';

	public static function tableName(): string
	{
		return 'task_event';
	}

	public function rules(): array
	{
		return [
			[['task_history_id', 'event_type'], 'required'],
			[['task_history_id'], 'integer'],
			[['event_type'], 'string', 'max' => 32],
			[['created_at'], 'safe'],
			[['task_history_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskHistory::class, 'targetAttribute' => ['task_history_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'              => 'ID',
			'task_history_id' => 'Task History ID',
			'event_type'      => 'Event Type',
			'created_at'      => 'Created At'
		];
	}

	public function getTaskHistory(): TaskHistoryQuery
	{
		/* @var TaskHistoryQuery */
		return $this->hasOne(TaskHistory::class, ['id' => 'task_history_id']);
	}
}