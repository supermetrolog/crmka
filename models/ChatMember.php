<?php

namespace app\models;

use app\kernel\common\models\AR;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ActiveQuery\ChatMemberQuery;
use yii\db\ActiveQuery;
use yii\db\Connection;
use Yii;

/**
 * This is the model class for table "chat_member".
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ChatMemberMessage[] $chatMemberMessages
 * @property ChatMemberMessage[] $chatMemberMessages0
 */
class ChatMember extends AR
{

    public static function tableName(): string
    {
        return 'chat_member';
    }

    public function rules(): array
    {
        return [
            [['model_type', 'model_id'], 'required'],
            [['model_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['model_type'], 'string', 'max' => 255],
            [['model_type', 'model_id'], 'unique', 'targetAttribute' => ['model_type', 'model_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'model_type' => 'Model Type',
            'model_id' => 'Model ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

	/**
	 * @return ActiveQuery|ChatMemberMessageQuery
	 */
    public function getChatMemberMessages(): ChatMemberMessageQuery
    {
        return $this->hasMany(ChatMemberMessage::className(), ['from_chat_member_id' => 'id']);
    }

	/**
	 * @return ActiveQuery|ChatMemberMessageQuery
	 */
    public function getChatMemberMessages0(): ChatMemberMessageQuery
    {
        return $this->hasMany(ChatMemberMessage::className(), ['to_chat_member_id' => 'id']);
    }


    public static function find(): ChatMemberQuery
    {
        return new ChatMemberQuery(get_called_class());
    }
}
