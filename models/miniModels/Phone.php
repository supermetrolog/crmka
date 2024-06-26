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
 * @property int|null $isMain
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
            [['contact_id', 'isMain'], 'integer'],
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
            'isMain' => 'IsMain',
        ];
    }
    public static function isValidPhoneNumber(string $number): bool
    {
        if (strlen($number) !== 11) {
            return false;
        }
        if ($number[0] != "7") {
            return false;
        }

        return true;
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
        $fields['native_phone'] = function ($fields) {
            return $fields['phone'];
        };
        $fields['phone'] = function ($fields) {
            if (self::isValidPhoneNumber($fields['phone'])) {
                return PhoneFormatter::format($fields['phone']);
            }
            return $fields['phone'];
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
