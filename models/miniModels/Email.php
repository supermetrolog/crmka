<?php

namespace app\models\miniModels;

use app\models\Contact;

use Yii;

/**
 * This is the model class for table "email".
 *
 * @property int $id
 * @property int $contact_id
 * @property string $email
 *
 * @property Contact $contact
 */
class Email extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'email';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contact_id', 'email'], 'required'],
            [['contact_id'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_id' => 'Contact ID',
            'email' => 'Email',
        ];
    }

    /**
     * Gets query for [[Contact]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }
}
