<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ChatMemberLastEventQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "chat_member_last_event".
 *
 * @property int $id
 * @property int $chat_member_id
 * @property int $event_chat_member_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ChatMember $chatMember
 * @property ChatMember $eventChatMember
 */
class ChatMemberLastEvent extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

    public static function tableName(): string
    {
        return 'chat_member_last_event';
    }

    public function rules(): array
    {
        return [
            [['chat_member_id', 'event_chat_member_id'], 'required'],
            [['chat_member_id', 'event_chat_member_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['chat_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMember::className(), 'targetAttribute' => ['chat_member_id' => 'id']],
            [['event_chat_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMember::className(), 'targetAttribute' => ['event_chat_member_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'chat_member_id' => 'Chat Member ID',
            'event_chat_member_id' => 'Event Chat Member ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
	 * @return ActiveQuery|ChatMemberQuery
	 */
    public function getEventChatMember(): ChatMemberQuery
    {
        return $this->hasOne(ChatMember::className(), ['id' => 'event_chat_member_id']);
    }


    public static function find(): ChatMemberLastEventQuery
    {
        return new ChatMemberLastEventQuery(get_called_class());
    }
}
