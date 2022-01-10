<?php

namespace app\models;

use app\exceptions\ValidationErrorHttpException;
use yii\web\NotFoundHttpException;
use app\models\miniModels\RequestDeal;
use Yii;
use yii\data\ActiveDataProvider;
use app\models\miniModels\RequestDirection;
use app\models\miniModels\RequestDistrict;
use app\models\miniModels\RequestGateType;
use app\models\miniModels\RequestObjectClass;
use app\models\miniModels\RequestObjectType;
use app\models\miniModels\RequestRegion;
use ReflectionClass;

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
 * @property int|null $antiDustOnly [флаг] Только антипыль
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
 * @property string|null $movingDate Дата переезда
 * @property int|null $unknownMovingDate [флаг] Нет конкретики по сроку переезда/рассматривает постоянно
 *
 * @property Company $company
 * @property User $consultant
 * @property RequestDirection[] $requestDirections
 * @property RequestDistrict[] $requestDistricts
 * @property RequestGateType[] $requestGateTypes
 * @property RequestObjectClass[] $requestObjectClasses
 * @property RequestObjectType[] $requestObjectTypes
 * @property RequestRegion[] $requestRegions
 * @property Timeline[] $timelines
 */
class Request extends \yii\db\ActiveRecord
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_PASSIVE = 0;
    public const STATUS_DONE = 2;
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
            [['company_id', 'dealType', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'heated', 'consultant_id'], 'required'],
            [['antiDustOnly', 'expressRequest', 'firstFloorOnly', 'distanceFromMKADnotApplicable'], 'boolean'],
            [['company_id', 'dealType', 'distanceFromMKAD', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'heated', 'status', 'trainLine', 'trainLineLength', 'consultant_id', 'pricePerFloor', 'electricity', 'haveCranes', 'unknownMovingDate'], 'integer'],
            [['created_at', 'updated_at', 'movingDate', 'expressRequest', 'distanceFromMKAD', 'distanceFromMKADnotApplicable', 'firstFloorOnly', 'trainLine', 'trainLineLength', 'pricePerFloor', 'electricity', 'haveCranes', 'unknownMovingDate'], 'safe'],
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
            'movingDate' => 'Moving Date',
            'unknownMovingDate' => 'Unknown Moving Date',
        ];
    }
    public static function findModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public static function changeStatus($request_id, $status)
    {
        $request = self::findModel($request_id);
        $request->status = $status;
        return $request->save(false);
    }
    public static function getCompanyRequestsList($company_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->joinWith(['consultant', 'directions', 'districts', 'gateTypes', 'objectClasses', 'objectTypes', 'regions', 'deal' => function ($query) {
                $query->with(['consultant']);
            }])->where(['request.company_id' => $company_id]),
            'pagination' => [
                'pageSize' => 1000,
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
    //функция для создания сразу нескольких строк в связанных моделях
    public  function createManyMiniModels($className, $data)
    {
        $columnName = $className::MAIN_COLUMN;

        if (!$data || !$className || !$columnName) {
            return false;
        }
        [];
        $class = new ReflectionClass($className);

        foreach ($data as $item) {
            $model = $class->newInstance();
            $array['request_id'] = $this->id;
            $array[$columnName] = $item;
            if ($model->load($array, '') && $model->save()) {
                continue;
            } else {
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
            }
        }
        return true;
    }
    public static function createRequest($post_data)
    {
        $db = Yii::$app->db;
        $request = new Request();
        $transaction = $db->beginTransaction();
        try {
            if ($request->load($post_data, '') && $request->save()) {
                $request->createManyMiniModels(RequestDirection::class,  $post_data['directions']);
                $request->createManyMiniModels(RequestDistrict::class,  $post_data['districts']);
                $request->createManyMiniModels(RequestGateType::class,  $post_data['gateTypes']);
                $request->createManyMiniModels(RequestObjectClass::class,  $post_data['objectClasses']);
                $request->createManyMiniModels(RequestObjectType::class,  $post_data['objectTypes']);
                $request->createManyMiniModels(RequestRegion::class,  $post_data['regions']);
                Timeline::createNewTimeline($request->id, $request->consultant_id);
                // $transaction->rollBack();

                $transaction->commit();
                return ['message' => "Запрос создан", 'data' => $request->id];
            }
            throw new ValidationErrorHttpException($request->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public function updateManyMiniModels($className, $data)
    {
        $className::deleteAll(['request_id' => $this->id]);
        $this->createManyMiniModels($className, $data);
    }
    public static function updateRequest($request, $post_data)
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($request->load($post_data, '') && $request->save()) {
                $request->updateManyMiniModels(RequestDirection::class,  $post_data['directions']);
                $request->updateManyMiniModels(RequestDistrict::class,  $post_data['districts']);
                $request->updateManyMiniModels(RequestGateType::class,  $post_data['gateTypes']);
                $request->updateManyMiniModels(RequestObjectClass::class,  $post_data['objectClasses']);
                $request->updateManyMiniModels(RequestObjectType::class,  $post_data['objectTypes']);
                $request->updateManyMiniModels(RequestRegion::class,  $post_data['regions']);
                Timeline::updateConsultant($request->id, $request->consultant_id);

                // $transaction->rollBack();

                $transaction->commit();
                return ['message' => "Запрос изменен", 'data' => $request->id];
            }
            throw new ValidationErrorHttpException($request->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['movingDate'] = function ($fields) {
            if ($fields['movingDate']) {
                return date('Y-m-d', strtotime($fields['movingDate']));
            }
            return $fields['movingDate'];
        };
        return $fields;
    }
    public function extraFields()
    {
        $extraFields = parent::extraFields();
        return $extraFields;
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
     * Gets query for [[RequestDeal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeal()
    {
        return $this->hasOne(RequestDeal::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[RequestDirections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirections()
    {
        return $this->hasMany(RequestDirection::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[RequestDistricts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistricts()
    {
        return $this->hasMany(RequestDistrict::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[RequestGateTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGateTypes()
    {
        return $this->hasMany(RequestGateType::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[RequestObjectClasses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectClasses()
    {
        return $this->hasMany(RequestObjectClass::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[RequestObjectTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectTypes()
    {
        return $this->hasMany(RequestObjectType::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[RequestRegions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(RequestRegion::className(), ['request_id' => 'id']);
    }

    /**
     * Gets query for [[Timelines]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelines()
    {
        return $this->hasMany(Timeline::className(), ['request_id' => 'id']);
    }
}
