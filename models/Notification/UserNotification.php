<?php

namespace app\models\Notification;

use app\kernel\common\models\AR\AR;
use app\models\User;

/**
 * This is the model class for table "user_notification".
 *
 * @property int         $id
 * @property int         $mailing_id
 * @property int         $user_id
 * @property string|null $notified_at
 * @property string      $created_at
 * @property string      $updated_at
 *
 * @property Mailing     $mailing
 * @property User        $user
 */
class UserNotification extends AR
{

	public static function tableName(): string
	{
		return 'user_notification';
	}

	public function rules(): array
	{
		return [
			[['mailing_id', 'user_id'], 'required'],
			[['mailing_id', 'user_id'], 'integer'],
			[['notified_at', 'created_at', 'updated_at'], 'safe'],
			[['mailing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mailing::className(), 'targetAttribute' => ['mailing_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'          => 'ID',
			'mailing_id'  => 'Mailing ID',
			'user_id'     => 'User ID',
			'notified_at' => 'Notified At',
			'created_at'  => 'Created At',
			'updated_at'  => 'Updated At',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery|\app\models\ActiveQuery\MailingQuery
	 */
	public function getMailing(): \app\models\ActiveQuery\MailingQuery
	{
		return $this->hasOne(Mailing::className(), ['id' => 'mailing_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery|\app\models\ActiveQuery\UserQuery
	 */
	public function getUser(): \app\models\ActiveQuery\UserQuery
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}


	public static function find(): \app\models\ActiveQuery\UserNotificationQuery
	{
		return new \app\models\ActiveQuery\UserNotificationQuery(get_called_class());
	}
}
