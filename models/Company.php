<?php

namespace app\models;

use app\behaviors\CreateManyMiniModelsBehaviors;
use yii\data\ActiveDataProvider;
use app\exceptions\ValidationErrorHttpException;
use app\models\miniModels\CompanyFile;
use Yii;
use yii\data\Sort;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string|null $nameEng
 * @property string|null $nameRu
 * @property int|null $noName
 * @property int|null $formOfOrganization
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
 * @property int/null $processed
 * @property int $rating
 * @property string|null $description
 * @property int|null $passive_why
 * @property string|null $passive_why_comment
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $broker
 * @property Companygroup $companyGroup
 * @property User $consultant
 */
class Company extends \yii\db\ActiveRecord
{
    public const FORM_OF_ORGANIZATION_LIST = [
        0 => 'ООО',
        1 => 'ОАО',
        2 => 'ЗАО',
        3 => 'ПАО',
        4 => 'АО',
        5 => 'ИП',
    ];

    public function behaviors()
    {
        return [
            CreateManyMiniModelsBehaviors::class
        ];
    }

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
            [['noName', 'companyGroup_id', 'status', 'consultant_id', 'broker_id', 'activityGroup', 'activityProfile', 'formOfOrganization', 'processed', 'passive_why', 'rating'], 'integer'],
            [['consultant_id', 'activityGroup', 'activityProfile'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['nameEng', 'nameRu', 'officeAdress', 'legalAddress', 'ogrn', 'inn', 'kpp', 'checkingAccount', 'correspondentAccount', 'inTheBank', 'bik', 'okved', 'okpo', 'signatoryName', 'signatoryMiddleName', 'signatoryLastName', 'basis', 'documentNumber', 'passive_why_comment'], 'string', 'max' => 255],
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
            'processed' => 'Processed',
            'rating' => 'Rating',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'passive_why' => 'PassiveWhy',
            'passive_why_comment' => 'PassiveWhyComment',
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
            return count($this->deals);
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
        $fields['created_at_format'] = function ($fields) {
            return Yii::$app->formatter->format($fields['created_at'], 'datetime');
        };
        $fields['updated_at_format'] = function ($fields) {
            return Yii::$app->formatter->format($fields['updated_at'], 'datetime');
        };
        $fields['full_name'] = function ($fields) {
            $formOfOrganization = $fields['formOfOrganization'];
            $nameEng = $fields['nameEng'];
            $nameRu = $fields['nameRu'];
            $noName = $fields['noName'];
            if ($noName) {
                return "-";
            }
            $name = "";
            if ($formOfOrganization !== null) {
                $name .= self::FORM_OF_ORGANIZATION_LIST[$formOfOrganization];
            }

            if ($nameRu) {
                $name .= " $nameRu";
            }
            if ($nameEng) {
                if ($nameRu) {
                    $name .= " - $nameEng";
                } else {
                    $name .= " $nameEng";
                }
            }
            return trim($name);
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
            'query' => self::find()->joinWith(['requests'])->with([
                'requests' => function ($query) {
                    $query->where(['status' => Request::STATUS_ACTIVE]);
                },
                'companyGroup', 'broker', 'deals', 'consultant' => function ($query) {
                    $query->with('userProfile');
                }, 'productRanges', 'categories', 'contacts' => function ($query) {
                    $query->with(['phones', 'emails', 'contactComments']);
                }
            ]),
            'pagination' => [
                'pageSize' => 0,
            ],
            'sort' => [
                'attributes' => [
                    'default' => [
                        'asc' => ['request.created_at' => SORT_ASC, 'company.rating' => SORT_ASC, 'company.created_at' => SORT_ASC],
                        'desc' => ['request.created_at' => SORT_DESC, 'company.rating' => SORT_DESC, 'company.created_at' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'company.rating' => [
                        'asc' => ['company.rating' => SORT_ASC],
                        'desc' => ['company.rating' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'company.created_at' => [
                        'asc' => ['company.created_at' => SORT_ASC],
                        'desc' => ['company.created_at' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                ],

            ]
        ]);

        return $dataProvider;
    }
    public static function getCompanyInfo($id)
    {
        return self::find()->with(['productRanges', 'categories', 'companyGroup', 'broker', 'deals', 'consultant' => function ($query) {
            $query->with(['userProfile']);
        }, 'files', 'contacts' => function ($query) {
            $query->with(['phones', 'emails', 'contactComments', 'websites']);
        }])->where(['company.id' => $id])->one();
    }

    private function createGeneralContact($post_data)
    {
        if (!count($post_data['phones']) && !count($post_data['emails']) && !count($post_data['websites'])) return;
        $post_data['company_id'] = $this->id;
        $post_data['type'] = Contact::GENERAL_CONTACT_TYPE;
        return Contact::createContact($post_data);
    }
    private function updateGeneralContact($post_data)
    {
        $model = Contact::find()->where(['company_id' => $this->id, 'type' => Contact::GENERAL_CONTACT_TYPE])->one();
        if (!$model) {
            return $this->createGeneralContact($post_data);
        }
        return Contact::updateContact($model, $post_data);
    }
    private function updateFiles($post_data, $uploadFileModel)
    {
        CompanyFile::deleteAll(['company_id' => $this->id]);
        foreach ($post_data['files'] as $file) {
            $model = new CompanyFile();
            if (!$model->load($file, '') || !$model->save()) {
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
            }
        }
        $this->uploadFiles($uploadFileModel);
    }

    private function uploadFiles($uploadFileModel)
    {
        foreach ($uploadFileModel->files as $file) {
            if (!$uploadFileModel->uploadOne($file)) {
                throw new ValidationErrorHttpException($uploadFileModel->getErrorSummary(false));
            }
            $companyFileModel = new CompanyFile();
            $companyFileModel->company_id = $this->id;
            $companyFileModel->name = $file->name;
            $companyFileModel->type = $file->type;
            $companyFileModel->filename = $uploadFileModel->filename;
            $companyFileModel->size = (string)$file->size;
            if (!$companyFileModel->save()) {
                throw new ValidationErrorHttpException($companyFileModel->getErrorSummary(false));
            }
        }
    }
    public static function createCompany($post_data, $uploadFileModel)
    {
        $db = Yii::$app->db;
        $model = new Company();
        $transaction = $db->beginTransaction();
        try {
            if ($model->load($post_data, '') && $model->save()) {
                $model->createManyMiniModels([
                    Category::class =>  $post_data['categories'],
                    Productrange::class => $post_data['productRanges'],
                ]);
                $model->createGeneralContact($post_data['contacts']);
                $model->uploadFiles($uploadFileModel);
                // $transaction->rollBack();

                $transaction->commit();
                return ['message' => "Компания создана", 'data' => $model->id];
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }

    public static function updateCompany(Company $model, $post_data, $uploadFileModel = [])
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($model->load($post_data, '') && $model->save()) {
                $model->updateManyMiniModels([
                    Category::class =>  $post_data['categories'],
                    Productrange::class => $post_data['productRanges'],
                ]);
                $model->updateGeneralContact($post_data['contacts']);
                $model->updateFiles($post_data, $uploadFileModel);
                // $transaction->rollBack();

                $transaction->commit();
                return ['message' => "Компания изменена", 'data' => $model->id];
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
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
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(CompanyFile::className(), ['company_id' => 'id']);
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
     * Gets query for [[Deals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeals()
    {
        return $this->hasMany(Deal::className(), ['company_id' => 'id']);
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
    /**
     * Gets query for [[Contacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Request::className(), ['company_id' => 'id']);
    }
}
