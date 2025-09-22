<?php

namespace app\models\User;

use app\components\Notification\Interfaces\NotifiableInterface;
use app\components\Notification\Interfaces\TelegramNotifiableInterface;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\UserAccessTokenQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\ChatMember;
use app\models\Contact;
use Exception;
use Yii;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\filters\auth\HttpBearerAuth;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int                    $id
 * @property string                 $username
 * @property string                 $password_hash
 * @property string|null            $password_reset_token
 * @property string                 $email
 * @property string                 $email_username
 * @property string                 $email_password
 * @property int                    $status
 * @property int                    $created_at
 * @property int                    $updated_at
 * @property int                    $role
 * @property int                    $user_id_old
 * @property ?string                $last_seen
 * @property bool                   $restrict_ip_login
 *
 * @property UserProfile            $userProfile
 * @property ChatMember             $chatMember
 * @property-read UserAccessToken[] $userAccessTokens
 * @property-read ?UserTelegramLink $userTelegramAccount
 */
class User extends AR implements IdentityInterface, NotifiableInterface, TelegramNotifiableInterface
{
	public const ACTIVITY_TIMEOUT = 300; // 5 minutes

	const STATUS_DELETED  = 0;
	const STATUS_INACTIVE = 9;
	const STATUS_ACTIVE   = 10;

	const ROLE_DEFAULT    = 1;
	const ROLE_CONSULTANT = 2;
	const ROLE_MODERATOR  = 3;
	const ROLE_OWNER      = 4;
	const ROLE_ADMIN      = 5;
	const ROLE_SYSTEM     = 6;

	protected bool $useUnixSoftUpdate = true;
	protected bool $useUnixSoftCreate = true;

	public static function getRoles(): array
	{
		return [
			self::ROLE_DEFAULT,
			self::ROLE_CONSULTANT,
			self::ROLE_MODERATOR,
			self::ROLE_OWNER,
			self::ROLE_ADMIN,
			self::ROLE_SYSTEM
		];
	}

	public static function tableName(): string
	{
		return 'user';
	}

	public static function getMorphClass(): string
	{
		return 'user';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[
				[
					'username',
					'password_hash',
					'created_at',
					'updated_at',
					'role'
				],
				'required'
			],
			[['status', 'created_at', 'updated_at', 'role'], 'integer'],
			[
				[
					'username',
					'password_hash',
					'password_reset_token',
					'email',
					'email_password',
					'email_username'
				],
				'string',
				'max' => 255
			],
			[['last_seen'], 'safe'],
			[['username'], 'unique'],
			[['email'], 'unique'],
			[['password_reset_token'], 'unique'],
			['role', 'in', 'range' => self::getRoles()],
			['restrict_ip_login', 'boolean'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'                   => 'ID',
			'username'             => 'Username',
			'password_hash'        => 'Password Hash',
			'password_reset_token' => 'Password Reset Token',
			'email'                => 'Email',
			'status'               => 'Status',
			'created_at'           => 'Created At',
			'updated_at'           => 'Updated At',
			'last_seen'            => 'Last Seen',
		];
	}

	public function behaviors(): array
	{
		$behaviors                  = parent::behaviors();
		$behaviors['authenticator'] = [
			'class' => HttpBearerAuth::class,
		];

		return $behaviors;
	}

	public function getEmailForSend(): array
	{
		$defaultFrom = [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
		if (!$this->email_username || !$this->email_password || !$this->email) {
			return $defaultFrom;
		}

		return [$this->email => $this->userProfile->shortName];
	}

	public function getEmailUsername(): string
	{
		if (!$this->email_username || !$this->email_password) {
			return Yii::$app->params['senderUsername'];
		}

		return $this->email_username;
	}

	public function getEmailPassword(): string
	{
		if (!$this->email_username || !$this->email_password) {
			return Yii::$app->params['senderPassword'];
		}

		return $this->email_password;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUserProfile(): ActiveQuery
	{
		return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
	}

	/**
	 * @return ContactQuery
	 */
	public function getContacts(): ContactQuery
	{
		/** @var ContactQuery $query */
		$query = $this->hasMany(Contact::class, ['user_id' => 'id']);

		return $query;
	}

	/**
	 * @return UserAccessTokenQuery
	 */
	public function getUserAccessTokens(): UserAccessTokenQuery
	{
		/** @var UserAccessTokenQuery $query */
		$query = $this->hasMany(UserAccessToken::class, ['user_id' => 'id']);

		return $query;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentity($id)
	{
		return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		return self::find()->byStatus(self::STATUS_ACTIVE)->byAccessToken($token)->one();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId(): int
	{
		return $this->getPrimaryKey();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthKey()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function validateAuthKey($authKey)
	{
	}

	/**
	 * @return ChatMemberQuery|ActiveQuery
	 * @throws ErrorException
	 */
	public function getChatMember(): ChatMemberQuery
	{
		return $this->morphHasOne(ChatMember::class);
	}

	public function getUserTelegramAccount(): AQ
	{
		/** @var AQ */
		return $this->hasOne(UserTelegramLink::class, ['user_id' => 'id']);
	}

	public function getTelegramChatId(): ?int
	{
		$acc = $this->userTelegramAccount;

		return $acc ? $acc->chat_id : null;
	}

	/**
	 * @return UserQuery
	 */
	public static function find(): UserQuery
	{
		return new UserQuery(get_called_class());
	}

	public function getUserId(): int
	{
		return $this->id;
	}

	/**
	 * @return bool Whether the user is an administrator.
	 */
	public function isAdministrator(): bool
	{
		return $this->role === self::ROLE_ADMIN;
	}

	/**
	 * @return bool
	 */
	public function isOwner(): bool
	{
		return $this->role === self::ROLE_OWNER;
	}

	public function isSystem(): bool
	{
		return $this->role === self::ROLE_SYSTEM;
	}

	public function isModerator(): bool
	{
		return $this->role === self::ROLE_MODERATOR;
	}

	public function isModeratorOrHigher(): bool
	{
		return $this->role >= self::ROLE_MODERATOR;
	}

	/**
	 * @throws Exception
	 */
	public function isOnline(): bool
	{
		if (!$this->last_seen) {
			return false;
		}

		return (DateTimeHelper::unix() - DateTimeHelper::makeUnix($this->last_seen)) <= self::ACTIVITY_TIMEOUT;
	}

	public function isActive(): bool
	{
		return $this->status === self::STATUS_ACTIVE;
	}

	public function isInactive(): bool
	{
		return $this->status === self::STATUS_INACTIVE;
	}

	public function isDeleted(): bool
	{
		return $this->status === self::STATUS_DELETED;
	}

	public function isIpAccessRestricted(): bool
	{
		return $this->restrict_ip_login;
	}
}
