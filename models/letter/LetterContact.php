<?php

namespace app\models\letter;

use app\models\Contact;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use Yii;
use yii\validators\RequiredValidator;

/**
 * This is the model class for table "letter_contact".
 *
 * @property int $id
 * @property int $letter_id [СВЯЗЬ] с отправленными письмами
 * @property string|null $email email контакта
 * @property string|null $phone номер контакта
 * @property int $contact_id [СВЯЗЬ] с таблицей контактов
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
            [['letter_id', "contact_id"], 'required'],
            [['letter_id'], 'integer'],
            [['email', 'phone'], 'string'],
            ['email', 'validateContacts'],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Letter::className(), 'targetAttribute' => ['letter_id' => 'id']],
        ];
    }

    public function validateContacts()
    {
        $required = new RequiredValidator();
        if ($required->validate($this->email) && $required->validate($this->phone)) {
            $this->addError('contacts', 'phone or email connot be empty');
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
            'email' => 'Email',
            'phone' => 'Phone',
            'contact_id' => 'Contact ID',
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
        return $this->hasOne(Email::className(), ['id' => 'email']);
    }

    /**
     * Gets query for [[Phone]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhone()
    {
        return $this->hasOne(Phone::className(), ['id' => 'phone']);
    }
}
