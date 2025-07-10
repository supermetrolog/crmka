<?php

namespace app\models;

use app\models\ActiveQuery\CallQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\PhoneQuery;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\miniModels\Phone;
use yii\base\ErrorException;

/**
 * This is the model class for table "call".
 *
 * @property int               $id
 * @property int               $user_id
 * @property int|null          $contact_id
 * @property int|null          $phone_id
 * @property int               $type
 * @property int               $status
 * @property ?string           $description
 * @property string            $created_at
 * @property string            $updated_at
 * @property ?string           $deleted_at
 *
 * @property-read User         $user
 * @property-read Contact      $contact
 * @property-read ChatMember[] $chatMembers
 * @property-read Survey[]     $surveys
 * @property-read ?Phone       $phone
 */
class Call extends \app\kernel\common\models\AR\AR
{
	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public const STATUS_MISSED        = 0; // не ответил
	public const STATUS_COMPLETED     = 1; // успешно поговорили
	public const STATUS_BUSY          = 2; // занят
	public const STATUS_NOT_AVAILABLE = 3; // не доступен
	public const STATUS_REJECTED      = 4; // отклонен
	public const STATUS_ANGRY         = 5;
	public const STATUS_BLOCKED       = 6; // заблокирован
	public const STATUS_NOT_EXISTS    = 7; // не существует

	public const TYPE_OUTGOING = 0;
	public const TYPE_INCOMING = 1;

	public static function getStatuses(): array
	{
		return [
			self::STATUS_MISSED,
			self::STATUS_COMPLETED,
			self::STATUS_BUSY,
			self::STATUS_NOT_AVAILABLE,
			self::STATUS_REJECTED,
			self::STATUS_ANGRY,
			self::STATUS_BLOCKED,
			self::STATUS_NOT_EXISTS
		];
	}

	public static function getTypes(): array
	{
		return [
			self::TYPE_OUTGOING,
			self::TYPE_INCOMING
		];
	}

	public static function tableName(): string
	{
		return 'call';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'contact_id', 'type', 'status'], 'required'],
			[['user_id', 'contact_id', 'type', 'status', 'phone_id'], 'integer'],
			['description', 'string', 'max' => 512],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['contact_id'], 'exist', 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
			[['phone_id'], 'exist', 'targetClass' => Contact::class, 'targetAttribute' => ['phone_id' => 'id']],
		];
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getContact(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->hasOne(Contact::class, ['id' => 'contact_id']);
	}

	public function getPhone(): PhoneQuery
	{
		/** @var PhoneQuery */
		return $this->hasOne(Phone::class, ['id' => 'phone_id']);
	}

	public function isOutgoing(): bool
	{
		return $this->type === self::TYPE_OUTGOING;
	}

	public function isIncoming(): bool
	{
		return $this->type === self::TYPE_INCOMING;
	}

	public static function find(): CallQuery
	{
		return new CallQuery(static::class);
	}

	/**
	 * @throws ErrorException
	 */
	public function getRelationSecond(): RelationQuery
	{
		/** @var RelationQuery */
		return $this->morphHasMany(Relation::class, 'id', 'second');
	}

	/**
	 * @throws ErrorException
	 */
	public function getChatMembers(): ChatMemberQuery
	{
		/** @var ChatMemberQuery */
		return $this->morphHasManyVia(ChatMemberQuery::class, 'id', 'first')
		            ->via('relationSecond');
	}

	/**
	 * @throws ErrorException
	 */
	public function getSurveys(): SurveyQuery
	{
		/** @var SurveyQuery */
		return $this->morphHasManyVia(Survey::class, 'id', 'first')
		            ->andOnCondition(['!=', 'status', Survey::STATUS_DRAFT])
		            ->andOnCondition(['deleted_at' => null])
		            ->via('relationSecond');
	}
}
