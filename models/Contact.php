<?php

namespace app\models;

use Yii;
use app\models\miniModels\ContactComment;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use app\models\miniModels\Website;

/**
 * This is the model class for table "contact".
 *
 * @property int $id
 * @property int $company_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property int|null $status
 * @property int|null $type
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Company $company
 * @property ContactComment[] $contactComments
 * @property Email[] $emails
 * @property Phone[] $phones
 */
class Contact extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'first_name'], 'required'],
            [['company_id', 'status', 'type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'status' => 'Status',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    // public function fields()
    // {
    //     $fields = parent::fields();
    //     // unset($fields['nameEng']);

    //     // var_dump($fields);
    //     $fields['anal'] = function () {
    //         return rand(10, 100);
    //     };
    //     return $fields;
    // }


    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Gets query for [[ContactComments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContactComments()
    {
        return $this->hasMany(ContactComment::className(), ['contact_id' => 'id']);
    }

    /**
     * Gets query for [[Emails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(Email::className(), ['contact_id' => 'id']);
    }

    /**
     * Gets query for [[Phones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhones()
    {
        return $this->hasMany(Phone::className(), ['contact_id' => 'id']);
    }
    /**
     * Gets query for [[Websites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWebsites()
    {
        return $this->hasMany(Website::className(), ['contact_id' => 'id']);
    }
}
