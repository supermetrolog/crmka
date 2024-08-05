<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ReminderQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "reminder".
 *
 * @property int                     $id
 * @property int                     $user_id
 * @property string                  $message
 * @property int                     $status
 * @property string                  $created_by_type
 * @property int                     $created_by_id
 * @property string                  $notify_at
 * @property string                  $created_at
 * @property string                  $updated_at
 * @property string|null             $deleted_at
 * @property string                  $morph
 *
 * @property User                    $user
 * @property User                    $createdByUser
 * @property User                    $createdBy
 */
class Reminder extends AR
{
	public const STATUS_CREATED    = 1;
	public const STATUS_ACCEPTED   = 2;
	public const STATUS_DONE       = 3;
	public const STATUS_IMPOSSIBLE = 4;
	public const STATUS_LATER      = 5;

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;

    public static function tableName(): string
    {
        return 'reminder';
    }

    public function rules(): array
    {
        return [
            [['user_id', 'message', 'status', 'created_by_type', 'created_by_id', 'notify_at'], 'required'],
            [['user_id', 'status', 'created_by_id'], 'integer'],
            [['message'], 'string'],
            [['notify_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by_type', 'morph'], 'string', 'max' => 255],
			['status', 'in', 'range' => self::getStatuses()],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'                 => 'ID',
            'user_id'            => 'User ID',
            'message'            => 'Message',
            'status'             => 'Status',
            'created_by_type'    => 'Created By Type',
            'created_by_id'      => 'Created By ID',
            'notify_at'          => 'Notify At',
            'created_at'         => 'Created At',
            'updated_at'         => 'Updated At',
        ];
    }

	public static function getStatuses(): array
	{
		return [
			self::STATUS_CREATED,
			self::STATUS_ACCEPTED,
			self::STATUS_DONE,
			self::STATUS_IMPOSSIBLE,
			self::STATUS_LATER,
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
    public function getUser(): \yii\db\ActiveQuery
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

	public static function find(): ReminderQuery
	{
		return new ReminderQuery(get_called_class());
	}
}
