<?php

namespace app\models\Notification;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\MailingQuery;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "mailing".
 *
 * @property int                 $id
 * @property int                 $channel_id
 * @property string              $subject
 * @property string              $message
 * @property string              $created_by_type
 * @property int                 $created_by_id
 * @property string              $created_at
 * @property string              $updated_at
 *
 * @property NotificationChannel $channel
 * @property UserNotification[]  $userNotifications
 */
class Mailing extends AR
{

	public static function tableName(): string
	{
		return 'mailing';
	}

	public function rules(): array
	{
		return [
			[['channel_id', 'subject', 'message', 'created_by_type', 'created_by_id'], 'required'],
			[['channel_id', 'created_by_id'], 'integer'],
			[['message'], 'string'],
			[['created_at', 'updated_at'], 'safe'],
			[['subject', 'created_by_type'], 'string', 'max' => 255],
			[['channel_id'], 'exist', 'skipOnError' => true, 'targetClass' => NotificationChannel::className(), 'targetAttribute' => ['channel_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'              => 'ID',
			'channel_id'      => 'Channel ID',
			'subject'         => 'Subject',
			'message'         => 'Message',
			'created_by_type' => 'Created By Type',
			'created_by_id'   => 'Created By ID',
			'created_at'      => 'Created At',
			'updated_at'      => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery|NotificationChannelQuery
	 */
	public function getChannel(): NotificationChannelQuery
	{
		return $this->hasOne(NotificationChannel::className(), ['id' => 'channel_id']);
	}

	/**
	 * @return ActiveQuery|UserNotificationQuery
	 */
	public function getUserNotifications(): UserNotificationQuery
	{
		return $this->hasMany(UserNotification::className(), ['mailing_id' => 'id']);
	}


	public static function find(): MailingQuery
	{
		return new MailingQuery(get_called_class());
	}
}
