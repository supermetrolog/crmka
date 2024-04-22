<?php

namespace app\models;

use app\kernel\common\models\AR;
use app\models\ActiveQuery\ChatMemberMessageTaskQuery;
use app\models\ActiveQuery\TaskQuery;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "task".
 *
 * @property int                     $id
 * @property int                     $user_id
 * @property string                  $message
 * @property int                     $status
 * @property string|null             $start
 * @property string|null             $end
 * @property string                  $created_by_type
 * @property int                     $created_by_id
 * @property string                  $created_at
 * @property string                  $updated_at
 * @property string                  $deleted_at
 *
 * @property ChatMemberMessageTask[] $chatMemberMessageTasks
 * @property User                    $user
 * @property User                    $createdByUser
 * @property User                    $createdBy
 */
class Task extends AR
{
	public const STATUS_CREATED    = 1;
	public const STATUS_ACCEPTED   = 2;
	public const STATUS_DONE       = 3;
	public const STATUS_IMPOSSIBLE = 4;

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;


	public static function tableName(): string
	{
		return 'task';
	}

	public function formName(): string
	{
		return '';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'message', 'status', 'created_by_type', 'created_by_id'], 'required'],
			[['user_id', 'status', 'created_by_id'], 'integer'],
			[['message'], 'string'],
			[['start', 'end', 'created_at', 'updated_at'], 'safe'],
			[['created_by_type'], 'string', 'max' => 255],
			['status', 'in', 'range' => self::getStatuses()],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
		];
	}

	public static function getStatuses(): array
	{
		return [
			self::STATUS_CREATED,
			self::STATUS_ACCEPTED,
			self::STATUS_DONE,
			self::STATUS_IMPOSSIBLE,
		];
	}

	/**
	 * @return ActiveQuery|ChatMemberMessageTaskQuery
	 */
	public function getChatMemberMessageTasks(): ChatMemberMessageTaskQuery
	{
		return $this->hasMany(ChatMemberMessageTask::className(), ['task_id' => 'id']);
	}

	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getCreatedByUser(): ActiveQuery
	{
		return $this->morphBelongTo(User::class, 'id', 'created_by');
	}

	public function getCreatedBy(): AR
	{
		return $this->createdByUser;
	}


	public static function find(): TaskQuery
	{
		return new TaskQuery(get_called_class());
	}
}
