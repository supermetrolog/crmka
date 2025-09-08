<?php

namespace app\models\Notification;

use app\components\Notification\Interfaces\StoredNotificationInterface;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\MailingQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\User;

/**
 * This is the model class for table "user_notification".
 *
 * @property int                              $id
 * @property int                              $mailing_id
 * @property ?int                             $template_id
 * @property int                              $user_id
 * @property ?string                          $notified_at
 * @property ?string                          $viewed_at
 * @property ?string                          $acted_at
 * @property ?string                          $expires_at
 * @property string                           $created_at
 * @property string                           $updated_at
 *
 * @property-read  Mailing                    $mailing
 * @property-read  User                       $user
 * @property-read  UserNotificationAction[]   $userNotificationActions
 * @property-read  UserNotificationRelation[] $userNotificationRelations
 * @property-read  UserNotificationTemplate   $userNotificationTemplate
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
			[['mailing_id', 'user_id', 'template_id'], 'integer'],
			[['notified_at', 'created_at', 'updated_at', 'viewed_at', 'acted_at'], 'safe'],
			[['mailing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mailing::className(), 'targetAttribute' => ['mailing_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			['template_id', 'exist', 'targetClass' => UserNotificationTemplate::class, 'targetAttribute' => 'id'],
		];
	}

	public function getMailing(): MailingQuery
	{
		/** @var MailingQuery */
		return $this->hasOne(Mailing::class, ['id' => 'mailing_id']);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}


	public static function find(): UserNotificationQuery
	{
		return new UserNotificationQuery(static::class);
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

	public function getUserNotificationActions(): AQ
	{
		/** @var AQ */
		return $this->hasMany(UserNotificationAction::class, ['user_notification_id' => 'id']);
	}

	public function getActions(): array
	{
		return $this->userNotificationActions;
	}

	public function getUserNotificationRelations(): AQ
	{
		/** @var AQ */
		return $this->hasMany(UserNotificationRelation::class, ['notification_id' => 'id']);
	}

	public function getRelations(): array
	{
		return $this->userNotificationRelations;
	}

	public function getUserNotificationTemplate(): AQ
	{
		/** @var AQ */
		return $this->hasOne(UserNotificationTemplate::class, ['id' => 'template_id']);
	}

	public function getTemplate(): ?UserNotificationTemplate
	{
		return $this->userNotificationTemplate;
	}

	public function getCreatedByUser(): ?User
	{
		return $this->mailing->createdByUser;
	}
}
