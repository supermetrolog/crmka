<?php

namespace app\models;

use app\kernel\common\models\AR;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberMessageTaskQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "chat_member_message".
 *
 * @property int $id
 * @property int $to_chat_member_id
 * @property int|null $from_chat_member_id
 * @property string|null $message
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ChatMember $fromChatMember
 * @property ChatMember $toChatMember
 * @property ChatMemberMessageTask[] $chatMemberMessageTasks
 */
class ChatMemberMessage extends AR
{

    public static function tableName(): string
    {
        return 'chat_member_message';
    }

    public function rules(): array
    {
        return [
            [['to_chat_member_id'], 'required'],
            [['to_chat_member_id', 'from_chat_member_id'], 'integer'],
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['from_chat_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMember::className(), 'targetAttribute' => ['from_chat_member_id' => 'id']],
            [['to_chat_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMember::className(), 'targetAttribute' => ['to_chat_member_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'to_chat_member_id' => 'To Chat Member ID',
            'from_chat_member_id' => 'From Chat Member ID',
            'message' => 'Message',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

	/**
	 * @return ActiveQuery|ChatMemberQuery
	 */
    public function getFromChatMember(): ChatMemberQuery
    {
        return $this->hasOne(ChatMember::className(), ['id' => 'from_chat_member_id']);
    }

	/**
	 * @return ActiveQuery|ChatMemberQuery
	 */
    public function getToChatMember(): ChatMemberQuery
    {
        return $this->hasOne(ChatMember::className(), ['id' => 'to_chat_member_id']);
    }

	/**
	 * @return ActiveQuery|ChatMemberMessageTaskQuery
	 */
    public function getChatMemberMessageTasks(): ChatMemberMessageTaskQuery
    {
        return $this->hasMany(ChatMemberMessageTask::className(), ['chat_member_message_id' => 'id']);
    }


    public static function find(): ChatMemberMessageQuery
    {
        return new ChatMemberMessageQuery(get_called_class());
    }
}
