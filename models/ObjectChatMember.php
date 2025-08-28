<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\ObjectChatMemberQuery;
use app\models\Company\Company;
use yii\base\ErrorException;
use yii\db\ActiveQuery;

/**
 * @property int     $id
 * @property int     $object_id
 * @property string  $type
 * @property string  $created_at
 * @property string  $updated_at
 * @property string  $morph
 *
 * @property Objects $object
 */
class ObjectChatMember extends AR
{
	public const RENT_OR_SALE_TYPE     = 'rent_or_sale';
	public const SUBLEASE_TYPE         = 'sublease';
	public const RESPONSE_STORAGE_TYPE = 'response_storage';

	public static function tableName(): string
	{
		return 'object_chat_member';
	}

	public static function getMorphClass(): string
	{
		return 'object';
	}

	public function rules(): array
	{
		return [
			[['object_id', 'type'], 'required'],
			[['object_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['type', 'morph'], 'string', 'max' => 255],
			[['object_id', 'type'], 'unique', 'targetAttribute' => ['object_id', 'type']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'object_id'  => 'Object ID',
			'type'       => 'Type',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}


	public static function find(): ObjectChatMemberQuery
	{
		return new ObjectChatMemberQuery(get_called_class());
	}

	/**
	 * @return ChatMemberQuery|ActiveQuery
	 * @throws ErrorException
	 */
	public function getChatMember(): ChatMemberQuery
	{
		return $this->morphHasOne(ChatMember::class);
	}

	/**
	 * @return AQ|ActiveQuery
	 * @throws ErrorException
	 */
	public function getObject(): AQ
	{
		return $this->hasOne(Objects::class, ['id' => 'object_id'])->from([Objects::tableName() => Objects::getTable()]);
	}

	/**
	 * @return AQ|ActiveQuery
	 * @throws ErrorException
	 */
	public function getCompany(): AQ
	{
		return $this->hasOne(Company::class, ['id' => 'company_id'])->from([Company::tableName() => Company::getTable()])->via('object');
	}

	public function isRentOrSale(): bool
	{
		return $this->type === self::RENT_OR_SALE_TYPE;
	}

	public function isSublease(): bool
	{
		return $this->type === self::SUBLEASE_TYPE;
	}

	public function isResponseStorage(): bool
	{
		return $this->type === self::RESPONSE_STORAGE_TYPE;
	}
}
