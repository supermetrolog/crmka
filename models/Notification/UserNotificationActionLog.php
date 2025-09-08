<?php

namespace app\models\Notification;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\User;

/**
 * @property int                         $id
 * @property int                         $user_notification_id
 * @property int                         $action_id
 * @property int                         $user_id
 * @property string                      $executed_at
 *
 * @property-read UserNotification       $userNotification
 * @property-read UserNotificationAction $userNotificationAction
 * @property-read User                   $user
 */
class UserNotificationActionLog extends AR
{
	public static function tableName(): string
	{
		return 'user_notification_action_log';
	}

	public function rules(): array
	{
		return [
			[['user_notification_id', 'action_id', 'user_id', 'executed_at'], 'required'],
			[['user_notification_id', 'action_id', 'user_id'], 'integer'],
			[['executed_at'], 'safe'],
			['user_notification_id', 'exist', 'targetClass' => UserNotification::class, 'targetAttribute' => 'id'],
			['action_id', 'exist', 'targetClass' => UserNotificationAction::class, 'targetAttribute' => 'id'],
			['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
		];
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}

	public function getUserNotification(): UserNotificationQuery
	{
		/** @var UserNotificationQuery */
		return $this->hasOne(UserNotification::class, ['id' => 'user_notification_id']);
	}

	public function getUser(): User
	{
		/** @var User */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getUserNotificationAction(): UserNotificationAction
	{
		/** @var UserNotificationAction */
		return $this->hasOne(UserNotificationAction::class, ['id' => 'action_id']);
	}
}
