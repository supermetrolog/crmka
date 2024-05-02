<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ActiveQuery\ObjectChatMemberQuery;
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
	 */
	public function getObject(): AQ
	{
		return $this->hasOne(Objects::class, ['id' => 'object_id']);
	}
}
