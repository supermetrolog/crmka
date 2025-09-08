<?php

namespace app\models\Notification;

use app\components\Notification\Interfaces\StoredNotificationActionInterface;
use app\enum\Notification\UserNotificationActionTypeEnum;
use app\helpers\DateTimeHelper;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserNotificationQuery;
use app\traits\JsonAttributeTrait;
use Exception;

/**
 * @property int                              $id
 * @property int                              $user_notification_id
 * @property string                           $type
 * @property string                           $code
 * @property string                           $label
 * @property ?string                          $icon
 * @property ?string                          $style
 * @property bool                             $confirmation
 * @property int                              $order
 * @property ?string                          $expires_at
 * @property string                           $created_at
 * @property string                           $updated_at
 * @property ?string                          $payload
 *
 * @property-read UserNotification            $userNotification
 * @property-read UserNotificationActionLog[] $userNotificationActionLogs
 */
class UserNotificationAction extends AR implements StoredNotificationActionInterface
{
	use JsonAttributeTrait;

	public static function tableName(): string
	{
		return 'user_notification_action';
	}

	public function rules(): array
	{
		return [
			[['user_notification_id', 'type', 'label', 'order', 'confirmation'], 'required'],
			[['user_notification_id', 'order'], 'integer'],
			['type', EnumValidator::class, 'enumClass' => UserNotificationActionTypeEnum::class],
			['code', 'string', 'max' => 32],
			[['label', 'icon'], 'string', 'max' => 64],
			['style', 'string', 'max' => 32],
			['confirmation', 'boolean'],
			['payload', 'string'],
			[['expires_at', 'created_at', 'updated_at'], 'safe'],
			['user_notification_id', 'exist', 'targetClass' => UserNotification::class, 'targetAttribute' => 'id'],
		];
	}

	public function fields(): array
	{
		$fields = parent::fields();

		$fields['payload'] = fn() => $this->getPayloadArray();

		return $fields;
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getIcon(): ?string
	{
		return $this->icon;
	}

	public function getStyle(): ?string
	{
		return $this->style;
	}

	public function getOrder(): int
	{
		return $this->order;
	}

	public function needConfirmation(): bool
	{
		return $this->confirmation;
	}

	/**
	 * @throws Exception
	 */
	public function getExpiresAt(): ?\DateTimeInterface
	{
		return DateTimeHelper::tryMake($this->expires_at);
	}

	public function getUserNotification(): UserNotificationQuery
	{
		/** @var UserNotificationQuery */
		return $this->hasOne(UserNotification::class, ['id' => 'user_notification_id']);
	}

	public function getUserNotificationActionLogs(): AQ
	{
		/** @var AQ */
		return $this->hasMany(UserNotificationActionLog::class, ['action_id' => 'id']);
	}

	public function getPayloadArray(): ?array
	{
		return $this->getJson('payload');
	}

	/**
	 * @param array|string|null $payload
	 */
	public function setPayloadArray($payload): void
	{
		$this->setJson('payload', $payload);
	}
}
