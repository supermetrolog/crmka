<?php

namespace app\models\letter;

use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use Yii;

/**
 * This is the model class for table "letter_contact".
 *
 * @property int $id
 * @property int $letter_id [СВЯЗЬ] с отправленными письмами
 * @property int $email_or_phone_id [СВЯЗЬ] с таблицей мейлов или номеров телефонов
 *
 * @property Letter $letter
 */
class LetterContact extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'letter_contact';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['letter_id', 'email_or_phone_id'], 'required'],
            [['letter_id', 'email_or_phone_id'], 'integer'],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::className(), 'targetAttribute' => ['letter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'letter_id' => 'Letter ID',
            'email_or_phone_id' => 'Email Or Phone ID',
        ];
    }

    /**
     * Gets query for [[Letter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLetter()
    {
        return $this->hasOne(Letter::className(), ['id' => 'letter_id']);
    }

    /**
     * Gets query for [[Email]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmail()
    {
        return $this->hasOne(Email::className(), ['id' => 'email_or_phone_id']);
    }
    /**
     * Gets query for [[Phone]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhone()
    {
        return $this->hasOne(Phone::className(), ['id' => 'email_or_phone_id']);
    }
}
