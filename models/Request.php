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
use app\behaviors\CreateManyMiniModelsBehaviors;
use app\events\NotificationEvent;
use app\models\miniModels\RequestObjectTypeGeneral;
use app\models\miniModels\TimelineStep;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property int $company_id [связь] ID компании
 * @property string $name Название
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
 * @property int|null $passive_why
 * @property string|null $passive_why_comment
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $movingDate Дата переезда
 * @property int|null $unknownMovingDate [флаг] Нет конкретики по сроку переезда/рассматривает постоянно
 * @property int|null $water [флаг]
 * @property int|null $sewerage [флаг]
 * @property int|null $gaz [флаг]
 * @property int|null $steam [флаг]
 * @property int|null $shelving [флаг]
 * @property int|null $outside_mkad [флаг] Вне мкад (если выбран регоин МОСКВА)
 * @property int|null $region_neardy [флаг] Регионы рядом
 * @property int|null $contact_id [связь] с контактом
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
    public const DEAL_TYPE_LIST = ['аренда', 'продажа', 'ответ-хранение', 'субаренда'];
    public const DEAL_TYPE_RENT = 0;
    public const DEAL_TYPE_SALE = 1;
    public const DEAL_TYPE_RESPONSE_STORAGE = 2;
    public const DEAL_TYPE_SUBLEASE = 3;



    public const REQUEST_CREATED_EVENT = 'request_created_event';
    public const REQUEST_UPDATED_EVENT = 'request_updated_event';


    public function init()
    {
        $this->on(self::REQUEST_CREATED_EVENT, [Yii::$app->notify, 'notifyUser']);
        parent::init();
    }

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
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'dealType', 'minArea', 'maxArea', 'minCeilingHeight', 'consultant_id', 'contact_id'], 'required'],
            [['heated', 'antiDustOnly', 'expressRequest', 'firstFloorOnly', 'distanceFromMKADnotApplicable'], 'boolean'],
            [['contact_id', 'region_neardy', 'outside_mkad', 'company_id', 'dealType', 'distanceFromMKAD', 'minArea', 'maxArea', 'minCeilingHeight', 'maxCeilingHeight', 'heated', 'status', 'trainLine', 'trainLineLength', 'consultant_id', 'pricePerFloor', 'electricity', 'haveCranes', 'unknownMovingDate', 'passive_why', 'water', 'sewerage', 'gaz', 'steam', 'shelving'], 'integer'],
            [['created_at', 'updated_at', 'movingDate', 'expressRequest', 'distanceFromMKAD', 'distanceFromMKADnotApplicable', 'firstFloorOnly', 'trainLine', 'trainLineLength', 'pricePerFloor', 'electricity', 'haveCranes', 'unknownMovingDate'], 'safe'],
            [['description', 'name'], 'string'],
            [['passive_why_comment', 'name'], 'string', 'max' => 255],
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
            'passive_why' => 'PassiveWhy',
            'passive_why_comment' => 'PassiveWhyComment',
            'water' => 'Water',
            'gaz' => 'Gaz',
            'sewerage' => 'Sewerage',
            'steam' => 'Steam',
            'shelving' => 'Shelving',
            'outside_mkad' => 'Outside MKAD',
            'region_neardy' => 'Region neardy',
            'contact_id' => 'Contact ID',
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
            'query' => self::find()->with(['contact.emails', 'contact.phones', 'consultant.userProfile', 'directions', 'districts', 'gateTypes', 'objectClasses', 'objectTypes', 'objectTypesGeneral',  'regions.info', 'deal.competitor', 'deal.offer', 'deal.consultant.userProfile'])->where(['request.company_id' => $company_id]),
            'pagination' => [
                'pageSize' => 0,
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
    public static function createRequest($post_data)
    {
        $db = Yii::$app->db;
        $request = new Request();
        $transaction = $db->beginTransaction();
        try {
            if ($request->load($post_data, '') && $request->save()) {
                $request->createManyMiniModels([
                    RequestDirection::class =>  $post_data['directions'],
                    RequestDistrict::class => $post_data['districts'],
                    RequestGateType::class => $post_data['gateTypes'],
                    RequestObjectClass::class => $post_data['objectClasses'],
                    RequestObjectType::class => $post_data['objectTypes'],
                    RequestObjectTypeGeneral::class => $post_data['objectTypesGeneral'],
                    RequestRegion::class => $post_data['regions'],
                ]);
                Timeline::createNewTimeline($request->id, $request->consultant_id);
                // $transaction->rollBack();
                $request->trigger(self::REQUEST_CREATED_EVENT, new NotificationEvent([
                    'consultant_id' => $request->consultant_id,
                    'type' => Notification::TYPE_REQUEST_INFO,
                    'title' => 'запрос',
                    'body' => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_request.php', ['model' => $request])
                ]));
                $transaction->commit();
                return ['message' => "Запрос создан", 'data' => $request->id];
            }
            throw new ValidationErrorHttpException($request->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public static function updateRequest($request, $post_data)
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $oldConsultantId = $request->consultant_id;
        try {
            $post_data['updated_at'] = date('Y-m-d H:i:s');
            if ($request->load($post_data, '') && $request->save()) {
                $request->updateManyMiniModels([
                    RequestDirection::class =>  $post_data['directions'],
                    RequestDistrict::class => $post_data['districts'],
                    RequestGateType::class => $post_data['gateTypes'],
                    RequestObjectClass::class => $post_data['objectClasses'],
                    RequestObjectType::class => $post_data['objectTypes'],
                    RequestObjectTypeGeneral::class => $post_data['objectTypesGeneral'],
                    RequestRegion::class => $post_data['regions'],
                ]);
                Timeline::updateConsultant($request->id, $request->consultant_id);
                if ($oldConsultantId != $request->consultant_id) {
                    $request->trigger(self::REQUEST_CREATED_EVENT, new NotificationEvent([
                        'consultant_id' => $request->consultant_id,
                        'type' => Notification::TYPE_REQUEST_INFO,
                        'title' => 'запрос',
                        'body' => Yii::$app->controller->renderFile('@app/views/notifications_template/assigned_request.php', ['model' => $request])
                    ]));
                    $request->trigger(self::REQUEST_CREATED_EVENT, new NotificationEvent([
                        'consultant_id' => $oldConsultantId,
                        'type' => Notification::TYPE_REQUEST_INFO,
                        'title' => 'запрос',
                        'body' => Yii::$app->controller->renderFile('@app/views/notifications_template/unAssigned_request.php', ['model' => $request])
                    ]));
                }

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
        $fields['movingDate_format'] = function ($fields) {
            return $fields['movingDate'] ? Yii::$app->formatter->format($fields['movingDate'], 'date') : null;
        };

        $fields['updated_at_format'] = function ($fields) {
            return $fields['updated_at'] ? Yii::$app->formatter->format($fields['updated_at'], 'datetime') : null;
        };
        $fields['created_at_format'] = function ($fields) {
            return $fields['created_at'] ? Yii::$app->formatter->format($fields['created_at'], 'datetime') : null;
        };
        $fields['format_name'] = function ($fields) {
            $name = $fields['name'] ? $fields['name'] . " - " : "";
            return $name . self::DEAL_TYPE_LIST[$fields['dealType']] . " {$fields['minArea']} - {$fields['maxArea']} м";
        };
        $fields['progress_percent'] = function () {
            return rand(10, 100);
        };
        $fields['format_ceilingHeight'] = function ($fields) {
            $min = $fields['minCeilingHeight'];
            $max = $fields['maxCeilingHeight'];
            if ($min && $max) {
                return "$min - $max";
            }
            return "от $min";
        };
        $fields['pricePerFloorMonth'] = function ($fields) {
            return round($fields['pricePerFloor'] !== null ? $fields['pricePerFloor'] / 12 : $fields['pricePerFloor'], 2);
        };


        return $fields;
    }
    public function extraFields()
    {
        $extraFields = parent::extraFields();
        $extraFields['timeline_progress'] = function ($extraFields) {
            $doneTimelineStepCount = Timeline::find()->joinWith(['timelineSteps'])->where(['timeline.request_id' => $this->id, 'timeline_step.status' => TimelineStep::STATUS_DONE, 'timeline.status' => Timeline::STATUS_ACTIVE])->count();
            if ($doneTimelineStepCount == null) return $doneTimelineStepCount;
            if ($doneTimelineStepCount == 0) return (int)$doneTimelineStepCount;
            $maxTimelineStepCount = 8;
            $percent = round(100 * $doneTimelineStepCount / $maxTimelineStepCount, 0);
            return $percent;
        };
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
     * Gets query for [[Contacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContacts()
    {
        return $this->hasMany(Contact::className(), ['request_id' => 'id']);
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
        return $this->hasOne(Deal::className(), ['request_id' => 'id']);
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
     * Gets query for [[RequestObjectTypesGeneral]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectTypesGeneral()
    {
        return $this->hasMany(RequestObjectTypeGeneral::className(), ['request_id' => 'id']);
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
