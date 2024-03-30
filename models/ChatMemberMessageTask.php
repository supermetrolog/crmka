<?php

namespace app\models;

use app\kernel\common\models\AR;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberMessageTaskQuery;
use app\models\ActiveQuery\TaskQuery;
use yii\db\ActiveQuery;
use yii\db\Connection;
use Yii;

/**
 * This is the model class for table "chat_member_message_task".
 *
 * @property int $id
 * @property int $task_id
 * @property int $chat_member_message_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ChatMemberMessage $chatMemberMessage
 * @property Task $task
 */
class ChatMemberMessageTask extends AR
{

    public static function tableName(): string
    {
        return 'chat_member_message_task';
    }

    public function rules(): array
    {
        return [
            [['task_id', 'chat_member_message_id'], 'required'],
            [['task_id', 'chat_member_message_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['chat_member_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMemberMessage::className(), 'targetAttribute' => ['chat_member_message_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'chat_member_message_id' => 'Chat Member Message ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

	/**
	 * @return ActiveQuery|ChatMemberMessageQuery
	 */
    public function getChatMemberMessage(): ChatMemberMessageQuery
    {
        return $this->hasOne(ChatMemberMessage::className(), ['id' => 'chat_member_message_id']);
    }

	/**
	 * @return ActiveQuery|TaskQuery
	 */
    public function getTask(): TaskQuery
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }


    public static function find(): ChatMemberMessageTaskQuery
    {
        return new ChatMemberMessageTaskQuery(get_called_class());
    }
}
