<?php

namespace app\models;

use Yii;
use app\exceptions\ValidationErrorHttpException;
use app\models\oldDb\OfferMix;

/**
 * This is the model class for table "deal".
 *
 * @property int $id
 * @property int $company_id [СВЯЗЬ] с компанией
 * @property int $competitor_company_id [СВЯЗЬ] с компанией (компания конкурент)
 * @property int|null $request_id [СВЯЗЬ] с запросом
 * @property int $consultant_id [СВЯЗЬ] с юзером
 * @property int|null $area площадь сделки
 * @property int|null $floorPrice цена пола
 * @property int|null $contractTerm срок контракта
 * @property string|null $dealDate дата сделки
 * @property string|null $clientLegalEntity юр. лицо клиента в сделке
 * @property string|null $description описание
 * @property string|null $name название сделки
 * @property int|null $object_id ID объекта из старой базы
 * @property int|null $complex_id ID комплекса из старой базы
 * @property string|null $competitor_name Название компании конкурента
 * @property int|null $is_our принадлежит ли сделка нашей компании
 * @property int|null $is_competitor принадлежит ли сделка  конкурентам
 * @property int $type_id
 * @property string $created_at
 * @property string|null $updated_at
 * @property int|null  $formOfOrganization
 * @property int  $status
 *
 * @property Company $company
 * @property User $consultant
 * @property Request $request
 */
class Deal extends \yii\db\ActiveRecord
{
    public const STATUS_DELETED = -1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'deal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'consultant_id', 'object_id', 'type_id', 'original_id', 'visual_id', 'complex_id'], 'required'],
            [['status', 'company_id', 'request_id', 'consultant_id', 'area', 'floorPrice', 'object_id', 'original_id', 'complex_id', 'competitor_company_id', 'is_our', 'is_competitor', 'contractTerm', 'formOfOrganization'], 'integer'],
            [['dealDate', 'created_at', 'updated_at'], 'safe'],
            [['clientLegalEntity', 'description', 'name', 'visual_id'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
            [['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::className(), 'targetAttribute' => ['request_id' => 'id']],
            [['competitor_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['competitor_company_id' => 'id']],
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
            'request_id' => 'Request ID',
            'consultant_id' => 'Consultant ID',
            'area' => 'Area',
            'floorPrice' => 'Floor Price',
            'clientLegalEntity' => 'Client Legal Entity',
            'description' => 'Description',
            'dealDate' => 'DealDate',
            'contaractTerm' => 'ContractTerm',
            'name' => 'Name',
            'object_id' => 'Object ID',
            'complex_id' => 'Complex ID',
            'competitor_company_id' => 'Competitor Company ID',
            'is_our' => 'Is Our',
            'is_competitor' => 'Is Competitor',
            'type_id' => 'Type ID',
            'formOfOrganization' => 'FormOfOrganization',
            'status' => 'Status',
        ];
    }
    public function fields()
    {
        $fields = parent::fields();
        $fields['dealDate_format'] = function ($fields) {
            return $fields['dealDate'] ? Yii::$app->formatter->format($fields['dealDate'], 'date') : null;
        };
        $fields['dealDate'] = function ($fields) {
            return $fields['dealDate'] ? date('Y-m-d', strtotime($fields['dealDate'])) : null;
        };
        $fields['clientLegalEntity_full_name'] = function ($fields) {
            if ($fields['formOfOrganization'] !== null) {
                return Company::FORM_OF_ORGANIZATION_LIST[$fields['formOfOrganization']] . ' ' . $fields['clientLegalEntity'];
            }
            return $fields['clientLegalEntity'];
        };
        return $fields;
    }

    public static function createDeal($post_data)
    {
        if ($post_data['request_id'] && $model = self::find()->where(['company_id' => $post_data['company_id'], 'request_id' => $post_data['request_id']])->one()) {
            $model->addError("request_id", 'Сделка для этого запроса уже существует!');
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
        $model = new self();
        if ($model->load($post_data, '') && $model->save()) {
            if ($model->request_id) {
                $request = Request::find()->where(['id' => $model->request_id])->limit(1)->one();
                $request->status = Request::STATUS_DONE;
                if (!$request->save()) {
                    throw new ValidationErrorHttpException($request->getErrorSummary(false));
                }
            }
            return ['message' => "Сделка создана", 'data' => $model->id];
        }
        throw new ValidationErrorHttpException($model->getErrorSummary(false));
    }

    public static function updateDeal($model, $post_data)
    {
        $post_data['updated_at'] = date('Y-m-d H:i:s');
        if ($model->load($post_data, '') && $model->save()) {
            return ['message' => "Сделка изменена", 'data' => $model->id];
        }
        throw new ValidationErrorHttpException($model->getErrorSummary(false));
    }
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
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor()
    {
        return $this->hasOne(Company::className(), ['id' => 'competitor_company_id']);
    }
    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitorCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'competitor_company_id']);
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
     * Gets query for [[Request]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'request_id']);
    }
    public function getOffer()
    {
        return $this->hasOne(OfferMix::class, ['object_id' => 'object_id', 'original_id' => 'original_id', 'type_id' => 'type_id']);
    }
}
