<?php

namespace app\models\Notification;

use app\components\Notification\Interfaces\StoredNotificationInterface;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\MailingQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\User;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_notification".
 *
 * @property int         $id
 * @property int         $mailing_id
 * @property int         $user_id
 * @property string|null $notified_at
 * @property string|null $viewed_at
 * @property string      $created_at
 * @property string      $updated_at
 *
 * @property Mailing     $mailing
 * @property User        $user
 */
class UserNotification extends AR implements StoredNotificationInterface
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
			[['notified_at', 'created_at', 'updated_at', 'viewed_at'], 'safe'],
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
			'viewed_at'   => 'Viewed At',
			'created_at'  => 'Created At',
			'updated_at'  => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery|MailingQuery
	 */
	public function getMailing(): MailingQuery
	{
		return $this->hasOne(Mailing::className(), ['id' => 'mailing_id']);
	}

	/**
	 * @return ActiveQuery|UserQuery
	 */
	public function getUser(): UserQuery
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}


	public static function find(): UserNotificationQuery
	{
		return new UserNotificationQuery(get_called_class());
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getSubject(): string
	{
		return $this->mailing->subject;
	}

	public function getMessage(): string
	{
		return $this->mailing->message;
	}
}
