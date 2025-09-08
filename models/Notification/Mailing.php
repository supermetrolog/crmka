<?php

namespace app\models\Notification;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\MailingQuery;
use app\models\ActiveQuery\NotificationChannelQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\User;

/**
 * This is the model class for table "mailing".
 *
 * @property int                 $id
 * @property int                 $channel_id
 * @property string              $subject
 * @property string              $message
 * @property ?string             $created_by_type
 * @property ?int                $created_by_id
 * @property string              $created_at
 * @property string              $updated_at
 *
 * @property NotificationChannel $channel
 * @property UserNotification[]  $userNotifications
 * @property ?User               $createdByUser
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
			[['channel_id', 'subject', 'message'], 'required'],
			[['channel_id', 'created_by_id'], 'integer'],
			[['message'], 'string'],
			[['created_at', 'updated_at'], 'safe'],
			[['subject', 'created_by_type'], 'string', 'max' => 255],
			[['channel_id'], 'exist', 'targetClass' => NotificationChannel::class, 'targetAttribute' => ['channel_id' => 'id']],
		];
	}

	public function getChannel(): NotificationChannelQuery
	{
		/** @var NotificationChannelQuery */
		return $this->hasOne(NotificationChannel::class, ['id' => 'channel_id']);
	}

	public function getUserNotifications(): UserNotificationQuery
	{
		/** @var UserNotificationQuery */
		return $this->hasMany(UserNotification::class, ['mailing_id' => 'id']);
	}


	public static function find(): MailingQuery
	{
		return new MailingQuery(static::class);
	}

	public function getCreatedByUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->morphBelongTo(User::class, 'id', 'created_by');
	}
}
