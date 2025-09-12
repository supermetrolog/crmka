<?php

namespace app\models\Notification;

use app\components\Notification\Interfaces\NotificationTemplateInterface;
use app\enum\Notification\UserNotificationTemplateCategoryEnum;
use app\enum\Notification\UserNotificationTemplatePriorityEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserNotificationQuery;
use app\traits\EnumAttributeLabelTrait;

/**
 * @property int                     $id
 * @property string                  $kind
 * @property string                  $category
 * @property string                  $priority
 * @property bool                    $is_active
 * @property string                  $created_at
 * @property string                  $updated_at
 *
 * @property-read UserNotification[] $userNotifications
 */
class UserNotificationTemplate extends AR implements NotificationTemplateInterface
{
	use EnumAttributeLabelTrait;

	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

	public static function tableName(): string
	{
		return 'user_notification_template';
	}

	public function rules(): array
	{
		return [
			[['kind', 'priority', 'category', 'is_active'], 'required'],
			[['kind'], 'string', 'max' => 32],
			[['category'], 'string', 'max' => 32],
			[['priority'], 'string', 'max' => 16],
			[['category'], EnumValidator::class, 'enumClass' => UserNotificationTemplateCategoryEnum::class],
			[['priority'], EnumValidator::class, 'enumClass' => UserNotificationTemplatePriorityEnum::class],
			['is_active', 'boolean'],
			[['created_at', 'updated_at'], 'safe']
		];
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}

	public function getKind(): string
	{
		return $this->kind;
	}

	public function getCategory(): string
	{
		return $this->category;
	}

	public function getCategoryLabel(): string
	{
		return $this->getEnumLabel('category', UserNotificationTemplateCategoryEnum::class);
	}

	public function getPriority(): string
	{
		return $this->priority;
	}

	public function getPriorityLabel(): string
	{
		return $this->getEnumLabel('priority', UserNotificationTemplatePriorityEnum::class);
	}

	public function isActive(): bool
	{
		return $this->is_active;
	}

	public function getUserNotifications(): UserNotificationQuery
	{
		/** @var UserNotificationQuery */
		return $this->hasMany(UserNotification::class, ['template_id' => 'id']);
	}
}
