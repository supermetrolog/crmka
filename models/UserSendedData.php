<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_sended_data".
 *
 * @property int $id
 * @property int $user_id
 * @property string $contact
 * @property int $contact_type
 * @property int $type
 * @property string $description
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property User $user
 */
class UserSendedData extends \yii\db\ActiveRecord
{
    public const PHONE_CONTACT_TYPE = 0;
    public const EMAIL_CONTACT_TYPE = 1;

    public const OBJECTS_SEND_FROM_TIMELINE_TYPE = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_sended_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'contact', 'contact_type', 'type', 'description'], 'required'],
            [['user_id', 'contact_type', 'type'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['contact'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'contact' => 'Contact',
            'contact_type' => 'Contact Type',
            'type' => 'Type',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
