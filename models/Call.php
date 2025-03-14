<?php

namespace app\models;

use app\models\ActiveQuery\CallQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "call".
 *
 * @property int         $id
 * @property int         $user_id
 * @property int|null    $contact_id
 * @property int         $type
 * @property int         $status
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 *
 * @property User        $user
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
			self::STATUS_BLOCKED
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
			[['user_id', 'contact_id', 'type', 'status'], 'integer'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
		];
	}


	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'user_id'    => 'User ID',
			'contact_id' => 'Contact ID',
			'type'       => 'Type',
			'status'     => 'Status',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getContact(): ActiveQuery
	{
		return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
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
		return new CallQuery(get_called_class());
	}
}
