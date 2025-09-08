<?php

namespace app\models\Notification;

use app\components\Notification\Interfaces\StoredNotificationRelationInterface;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\UserNotificationQuery;
use app\models\Request;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * @property int                   $id
 * @property int                   $notification_id
 * @property int                   $entity_id
 * @property string                $entity_type
 * @property string                $created_at
 * @property string                $updated_at
 *
 * @property-read UserNotification $userNotification
 *
 * @property-read ?Request         $entityRequest
 */
class UserNotificationRelation extends AR implements StoredNotificationRelationInterface
{
	public static function tableName(): string
	{
		return 'user_notification_relation';
	}

	public function rules(): array
	{
		return [
			[['notification_id', 'entity_type', 'entity_id'], 'required'],
			[['notification_id', 'entity_id'], 'integer'],
			['entity_type', 'string', 'max' => 255],
			[['created_at', 'updated_at'], 'safe'],
			['notification_id', 'exist', 'targetClass' => UserNotification::class, 'targetAttribute' => 'id']
		];
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}

	public function getUserNotification(): UserNotificationQuery
	{
		/** @var UserNotificationQuery */
		return $this->hasOne(UserNotification::class, ['id' => 'notification_id']);
	}

	public function morphHasOne(string $class, string $column = 'id', string $name = 'model', string $localColumn = 'morph'): ActiveQuery
	{
		return parent::morphHasOne($class,);
	}

	/**
	 * @throws ErrorException
	 */
	public function getEntityRequest(): RequestQuery
	{
		/** @var RequestQuery */
		return $this->morphHasOne(Request::class);
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getEntityType(): string
	{
		return $this->entity_type;
	}

	public function getEntityId(): int
	{
		return $this->entity_id;
	}
}
