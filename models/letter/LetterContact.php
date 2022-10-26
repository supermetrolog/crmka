<?php

namespace app\models\letter;

use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use Yii;
use yii\validators\RequiredValidator;

/**
 * This is the model class for table "letter_contact".
 *
 * @property int $id
 * @property int $letter_id [СВЯЗЬ] с отправленными письмами
 * @property int|null $email_id [СВЯЗЬ] с мейлами
 * @property int|null $phone_id [СВЯЗЬ] с телефонами
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
            [['letter_id'], 'required'],
            [['letter_id', 'email_id', 'phone_id'], 'integer'],
            ['email_id', 'validateContacts'],
            [['email_id'], 'exist', 'skipOnError' => true, 'targetClass' => Email::className(), 'targetAttribute' => ['email_id' => 'id']],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::className(), 'targetAttribute' => ['letter_id' => 'id']],
            [['phone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Phone::className(), 'targetAttribute' => ['phone_id' => 'id']],
        ];
    }

    public function validateContacts()
    {
        $required = new RequiredValidator();
        if ($required->validate($this->email_id) && $required->validate($this->phone_id)) {
            $this->addError('contacts', 'phone_id or email_id connot be empty');
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'letter_id' => 'Letter ID',
            'email_id' => 'Email ID',
            'phone_id' => 'Phone ID',
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
        return $this->hasOne(Email::className(), ['id' => 'email_id']);
    }

    /**
     * Gets query for [[Phone]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhone()
    {
        return $this->hasOne(Phone::className(), ['id' => 'phone_id']);
    }
}
