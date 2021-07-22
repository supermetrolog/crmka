<?php

namespace app\models;

use app\models\miniModels\RequestDirection;
use app\models\miniModels\RequestDistrict;
use app\models\miniModels\RequestGateType;
use app\models\miniModels\RequestObjectClass;
use app\models\miniModels\RequestObjectType;
use app\models\miniModels\RequestRegion;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property int $company_id [связь] ID компании
 * @property int $dealType Тип сделки
 * @property int|null $expressRequest [флаг] Срочный запрос
 * @property int|null $distanceFromMKAD Удаленность от МКАД
 * @property int|null $distanceFromMKADnotApplicable [флаг] Неприменимо
 * @property int $minArea Минимальная площадь пола
 * @property int $maxArea Максимальная площадь пола
 * @property int $minCeilingHeight Минимальная высота потолков
 * @property int $maxCeilingHeight максимальная высота потолков
 * @property int|null $firstFloorOnly [флаг] Только 1 этаж
 * @property int $heated [флаг] Отапливаемый
 * @property int $antiDustOnly [флаг] Только антипыль
 * @property int|null $trainLine [флаг] Ж/Д ветка
 * @property int|null $trainLineLength Длина Ж/Д
 * @property int $consultant_id [связь] ID консультанта
 * @property string|null $description Описание
 * @property int|null $pricePerFloor Цена за пол
 * @property int|null $electricity Электричество
 * @property int|null $haveCranes [флаг] Наличие кранов
 * @property int|null $status [флаг] Статус
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Company $company
 * @property User $consultant
 */
class Request extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'dealType', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'heated', 'antiDustOnly', 'consultant_id'], 'required'],
            [['company_id', 'dealType', 'expressRequest', 'distanceFromMKAD', 'distanceFromMKADnotApplicable', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'firstFloorOnly', 'heated', 'antiDustOnly', 'trainLine', 'trainLineLength', 'consultant_id', 'pricePerFloor', 'electricity', 'haveCranes', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['description'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
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
            'company_id' => 'Company ID',
            'dealType' => 'Deal Type',
            'expressRequest' => 'Express Request',
            'distanceFromMKAD' => 'Distance From Mkad',
            'distanceFromMKADnotApplicable' => 'Distance From Mka Dnot Applicable',
            'minArea' => 'Min Area',
            'maxArea' => 'Max Area',
            'minCeilingHeight' => 'Min Ceiling Height',
            'maxCeilingHeight' => 'Max Ceiling Height',
            'firstFloorOnly' => 'First Floor Only',
            'heated' => 'Heated',
            'antiDustOnly' => 'Anti Dust Only',
            'trainLine' => 'Train Line',
            'trainLineLength' => 'Train Line Length',
            'consultant_id' => 'Consultant ID',
            'description' => 'Description',
            'pricePerFloor' => 'Price Per Floor',
            'electricity' => 'Electricity',
            'haveCranes' => 'Have Cranes',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public static function getCompanyRequestsList($company_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->joinWith(['consultant', 'directions', 'districts', 'gateTypes', 'objectClasses', 'objectTypes', 'regions'])->where(['request.company_id' => $company_id]),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        return $dataProvider;
    }
    public static function getRequestInfo($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->joinWith(['consultant'])->where(['request.id' => $id]),
        ]);

        return $dataProvider;
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
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirections()
    {
        return $this->hasMany(RequestDirection::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[District]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistricts()
    {
        return $this->hasMany(RequestDistrict::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[GateType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGateTypes()
    {
        return $this->hasMany(RequestGateType::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[ObjectClass]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectClasses()
    {
        return $this->hasMany(RequestObjectClass::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[ObjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectTypes()
    {
        return $this->hasMany(RequestObjectType::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(RequestRegion::className(), ['request_id' => 'id']);
    }
}
