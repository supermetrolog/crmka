<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string|null $nameEng
 * @property string|null $nameRu
 * @property int|null $noName
 * @property string|null $formOfOrganization
 * @property int|null $companyGroup_id
 * @property string|null $officeAdress
 * @property int|null $status
 * @property int $consultant_id
 * @property int|null $broker_id
 * @property string|null $legalAddress
 * @property string|null $ogrn
 * @property string|null $inn
 * @property string|null $kpp
 * @property string|null $checkingAccount
 * @property string|null $correspondentAccount
 * @property string|null $inTheBank
 * @property string|null $bik
 * @property string|null $okved
 * @property string|null $okpo
 * @property string|null $signatoryName
 * @property string|null $signatoryMiddleName
 * @property string|null $signatoryLastName
 * @property string|null $basis
 * @property string|null $documentNumber
 * @property int $activityGroup
 * @property int $activityProfile
 * @property string|null $description
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $broker
 * @property Companygroup $companyGroup
 * @property User $consultant
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile'], 'integer'],
            [['consultant_id', 'activityGroup', 'activityProfile'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['nameEng', 'nameRu', 'formOfOrganization', 'officeAdress', 'legalAddress', 'ogrn', 'inn', 'kpp', 'checkingAccount', 'correspondentAccount', 'inTheBank', 'bik', 'okved', 'okpo', 'signatoryName', 'signatoryMiddleName', 'signatoryLastName', 'basis', 'documentNumber'], 'string', 'max' => 255],
            [['broker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['broker_id' => 'id']],
            [['companyGroup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companygroup::className(), 'targetAttribute' => ['companyGroup_id' => 'id']],
            [['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nameEng' => 'Name Eng',
            'nameRu' => 'Name Ru',
            'noName' => 'No Name',
            'formOfOrganization' => 'Form Of Organization',
            'companyGroup_id' => 'Company Group ID',
            'officeAdress' => 'Office Adress',
            'status' => 'Status',
            'consultant_id' => 'Consultant ID',
            'broker_id' => 'Broker ID',
            'legalAddress' => 'Legal Address',
            'ogrn' => 'Ogrn',
            'inn' => 'Inn',
            'kpp' => 'Kpp',
            'checkingAccount' => 'Checking Account',
            'correspondentAccount' => 'Correspondent Account',
            'inTheBank' => 'In The Bank',
            'bik' => 'Bik',
            'okved' => 'Okved',
            'okpo' => 'Okpo',
            'signatoryName' => 'Signatory Name',
            'signatoryMiddleName' => 'Signatory Middle Name',
            'signatoryLastName' => 'Signatory Last Name',
            'basis' => 'Basis',
            'documentNumber' => 'Document Number',
            'activityGroup' => 'Activity Group',
            'activityProfile' => 'Activity Profile',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function fields()
    {
        $fields = parent::fields();
        // unset($fields['nameEng']);

        // var_dump($fields);
        $fields['progress_percent'] = function () {
            return rand(10, 100);
        };
        $fields['deal_count'] = function () {
            return rand(10, 100);
        };
        $fields['request_count'] = function () {
            return rand(10, 100);
        };
        $fields['offer_count'] = function () {
            return rand(10, 100);
        };
        $fields['object_count'] = function () {
            return rand(10, 100);
        };
        $fields['category'] = function () {
            return rand(0, 5);
        };
        return $fields;
    }


    public function extraFields()
    {
        $extraFields = parent::extraFields();

        return $extraFields;
    }





    public static function getCompanyList()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->joinWith(['companyGroup', 'broker', 'consultant', 'productRanges', 'categories', 'contacts' => function ($query) {
                $query->with(['phones', 'emails', 'contactComments']);
            }]),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $dataProvider;
    }
    public static function getCompanyInfo($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->joinWith(['productRanges', 'categories', 'companyGroup', 'broker', 'consultant', 'contacts' => function ($query) {
                $query->with(['phones', 'emails', 'contactComments', 'websites']);
            }])->where(['company.id' => $id]),
        ]);

        return $dataProvider;
    }

    /**
     * Gets query for [[Broker]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBroker()
    {
        return $this->hasOne(User::className(), ['id' => 'broker_id']);
    }

    /**
     * Gets query for [[CompanyGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyGroup()
    {
        return $this->hasOne(Companygroup::className(), ['id' => 'companyGroup_id']);
    }

    /**
     * Gets query for [[Consultant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConsultant()
    {
        return $this->hasOne(User::className(), ['id' => 'consultant_id']);
    }
    /**
     * Gets query for [[Contacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['company_id' => 'id']);
    }
    /**
     * Gets query for [[categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['company_id' => 'id']);
    }
    /**
     * Gets query for [[productRanges]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductRanges()
    {
        return $this->hasMany(Productrange::className(), ['company_id' => 'id']);
    }
}
