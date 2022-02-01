<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "deal".
 *
 * @property int $id
 * @property int $company_id [СВЯЗЬ] с компанией
 * @property int|null $request_id [СВЯЗЬ] с запросом
 * @property int $consultant_id [СВЯЗЬ] с юзером
 * @property int|null $area площадь сделки
 * @property int|null $floorPrice цена пола
 * @property string|null $clientLegalEntity юр. лицо клиента в сделке
 * @property string|null $description описание
 * @property string|null $startEventTime врменя начала события
 * @property string|null $endEventTime врменя конца события
 * @property string|null $name название сделки
 * @property int|null $object_id ID объекта из старой базы
 * @property int|null $complex_id ID комплекса из старой базы
 * @property string|null $competitor_name Название компании конкурента
 * @property int|null $is_our принадлежит ли сделка нашей компании
 * @property int|null $is_competitor принадлежит ли сделка  конкурентам
 *
 * @property Company $company
 * @property User $consultant
 * @property Request $request
 */
class Deal extends \yii\db\ActiveRecord
{
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
            [['company_id', 'consultant_id'], 'required'],
            [['company_id', 'request_id', 'consultant_id', 'area', 'floorPrice', 'object_id', 'complex_id'], 'integer'],
            [['startEventTime', 'endEventTime'], 'safe'],
            [['clientLegalEntity', 'description', 'name'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
            [['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::className(), 'targetAttribute' => ['request_id' => 'id']],
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
            'startEventTime' => 'Start Event Time',
            'endEventTime' => 'End Event Time',
            'name' => 'Name',
            'object_id' => 'Object ID',
            'complex_id' => 'Complex ID',
            'competitor_name' => 'Competitor Name',
            'is_our' => 'Is Our',
            'is_competitor' => 'Is Competitor',
        ];
    }
    public function fields()
    {
        $fields = parent::fields();
        $fields['startEventTime'] = function ($fields) {
            if ($fields['startEventTime']) {
                return date('Y-m-d', strtotime($fields['startEventTime']));
            }
            return $fields['startEventTime'];
        };
        $fields['endEventTime'] = function ($fields) {
            if ($fields['endEventTime']) {
                return date('Y-m-d', strtotime($fields['endEventTime']));
            }
            return $fields['endEventTime'];
        };
        return $fields;
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
}
