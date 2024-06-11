<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberMessageViewQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "chat_member_message_view".
 *
 * @property int               $id
 * @property int               $chat_member_id
 * @property int               $chat_member_message_id
 * @property string            $created_at
 * @property string            $updated_at
 *
 * @property ChatMember        $chatMember
 * @property ChatMemberMessage $chatMemberMessage
 */
class ChatMemberMessageView extends AR
{
	public static function tableName(): string
	{
		return 'chat_member_message_view';
	}

	public function rules(): array
	{
		return [
			[['chat_member_id', 'chat_member_message_id'], 'required'],
			[['chat_member_id', 'chat_member_message_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['chat_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMember::className(), 'targetAttribute' => ['chat_member_id' => 'id']],
			[['chat_member_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMemberMessage::className(), 'targetAttribute' => ['chat_member_message_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'                     => 'ID',
			'chat_member_id'         => 'Chat Member ID',
			'chat_member_message_id' => 'Chat Member Message ID',
			'created_at'             => 'Created At',
			'updated_at'             => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery|ChatMemberQuery
	 */
	public function getChatMember(): ChatMemberQuery
	{
		return $this->hasOne(ChatMember::className(), ['id' => 'chat_member_id']);
	}

	/**
	 * @return ActiveQuery|ChatMemberMessageQuery
	 */
	public function getChatMemberMessage(): ChatMemberMessageQuery
	{
		return $this->hasOne(ChatMemberMessage::className(), ['id' => 'chat_member_message_id']);
	}

	public static function find(): ChatMemberMessageViewQuery
	{
		return new ChatMemberMessageViewQuery(get_called_class());
	}
}
