<?php

namespace app\models\miniModels;

use Yii;
use app\models\Contact;
use floor12\phone\PhoneFormatter;

/**
 * This is the model class for table "phone".
 *
 * @property int $id
 * @property int $contact_id
 * @property string $phone
 * @property string $exten
 *
 * @property Contact $contact
 */
class Phone extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'phone';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phone';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contact_id', 'phone'], 'required'],
            [['contact_id'], 'integer'],
            [['phone', 'exten'], 'string', 'max' => 255],
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
            'phone' => 'Phone',
            'exten' => 'Exten',
        ];
    }
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        preg_match_all('!\d+!', $this->phone, $numbers);
        $this->phone = implode('', $numbers[0]);
        return true;
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['phone'] = function ($fields) {
            return PhoneFormatter::format($fields['phone']);
        };
        return $fields;
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
