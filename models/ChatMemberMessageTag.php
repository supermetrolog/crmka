<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberMessageTagQuery;

/**
 * This is the model class for table "chat_member_message_tag".
 *
 * @property int         $id
 * @property string      $name
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class ChatMemberMessageTag extends AR
{

	public static function tableName(): string
	{
		return 'chat_member_message_tag';
	}

	public function rules(): array
	{
		return [
			[['name'], 'required'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['name'], 'string', 'max' => 255],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'name'       => 'Name',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'deleted_at' => 'Deleted At',
		];
	}


	public static function find(): ChatMemberMessageTagQuery
	{
		return new ChatMemberMessageTagQuery(get_called_class());
	}
}
