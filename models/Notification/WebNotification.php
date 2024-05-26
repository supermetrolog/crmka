<?php

namespace app\models\Notification;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\ActiveQuery\WebNotificationQuery;
use app\models\User;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "web_notification".
 *
 * @property int              $id
 * @property int              $user_id
 * @property int              $user_notification_id
 * @property string           $subject
 * @property string           $message
 * @property string|null      $viewed_at
 * @property string           $created_at
 * @property string           $updated_at
 *
 * @property User             $user
 * @property UserNotification $userNotification
 */
class WebNotification extends AR
{

	public static function tableName(): string
	{
		return 'web_notification';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'user_notification_id', 'subject', 'message'], 'required'],
			[['user_id', 'user_notification_id'], 'integer'],
			[['message'], 'string'],
			[['viewed_at', 'created_at', 'updated_at'], 'safe'],
			[['subject'], 'string', 'max' => 255],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			[['user_notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserNotification::className(), 'targetAttribute' => ['user_notification_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'                   => 'ID',
			'user_id'              => 'User ID',
			'user_notification_id' => 'User Notification ID',
			'subject'              => 'Subject',
			'message'              => 'Message',
			'viewed_at'            => 'Viewed At',
			'created_at'           => 'Created At',
			'updated_at'           => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery|UserQuery
	 */
	public function getUser(): UserQuery
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return ActiveQuery|UserNotificationQuery
	 */
	public function getUserNotification(): UserNotificationQuery
	{
		return $this->hasOne(UserNotification::className(), ['id' => 'user_notification_id']);
	}


	public static function find(): WebNotificationQuery
	{
		return new WebNotificationQuery(get_called_class());
	}
}
