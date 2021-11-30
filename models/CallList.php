<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "call_list".
 *
 * @property int $id
 * @property string $caller_id [связь] с user_profile (номер в системе Asterisk)
 * @property string $from кто звонит
 * @property string $to кому звонят
 * @property int $type [флаг] тип звонка (0 - исходящий / 1 - входящий
 * @property string|null $created_at
 *
 * @property UserProfile $caller
 */
class CallList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['caller_id', 'from', 'to', 'type'], 'required'],
            [['type'], 'integer'],
            [['created_at'], 'safe'],
            [['caller_id', 'from', 'to'], 'string', 'max' => 255],
            [['caller_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserProfile::className(), 'targetAttribute' => ['caller_id' => 'caller_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'caller_id' => 'Caller ID',
            'from' => 'From',
            'to' => 'To',
            'type' => 'Type',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Caller]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCaller()
    {
        return $this->hasOne(UserProfile::className(), ['caller_id' => 'caller_id']);
    }
}
