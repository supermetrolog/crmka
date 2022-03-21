<?php

namespace app\models;

use phpDocumentor\Reflection\Types\Self_;
use Yii;
use yii\data\ActiveDataProvider;


/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property int $consultant_id [связь] с юзером
 * @property string|null $title заголовок оповещения
 * @property string $body [html]текс оповещения
 * @property int $type тип оповещения
 * @property int|null $status
 * @property string|null $created_at
 *
 * @property User $consultant
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    private const FETCHED_STATUS = 0;
    public const NO_FETCHED_STATUS = -1;
    private const VIEWED_STATUS = 1;
    public const NO_VIEWED_STATUS = 0;
    public const PROCESSED_STATUS = 2;

    public const TYPE_COMPANY = 0;
    public const TYPE_REQUEST = 1;
    public const TYPE_CALENDAR = 2;
    public const TYPE_TIMELINE = 3;
    public const TYPE_COLLECTION = 4;
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['consultant_id', 'body', 'type'], 'required'],
            [['consultant_id', 'type', 'status'], 'integer'],
            [['created_at'], 'safe'],
            [['title', 'body'], 'string', 'max' => 255],
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
            'consultant_id' => 'Consultant ID',
            'title' => 'Title',
            'body' => 'Body',
            'type' => 'Type',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
    public static function viewed($id)
    {
        $models = self::find()->where(['consultant_id' => $id])->andWhere(['status' => self::NO_VIEWED_STATUS])->all();
        foreach ($models as $model) {
            // var_dump($model->status);

            if ($model->status == self::NO_VIEWED_STATUS) {
                $model->status = self::VIEWED_STATUS;
                $model->save();
            }
        }
    }
    public static function getNotificationsForUser($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->where(['notification.consultant_id' => $id]),
        ]);
        return $dataProvider;
    }
    public static function getNotificationsCount($id)
    {
        return self::find()->where(['notification.consultant_id' => $id, 'status' => [self::NO_FETCHED_STATUS, self::NO_VIEWED_STATUS]])->count();
    }
    public static function getNewNotifications($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->where(['notification.consultant_id' => $id])->andWhere(['status' => self::NO_FETCHED_STATUS]),
        ]);
        $models = $dataProvider->getModels();
        $response = self::array_copy($models);
        self::changeNoFetchedStatusToFetched($models);
        $dataProvider->models = $response;
        return $dataProvider;
    }
    public static function getUsersNewNotifications(array $ids)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->where(['notification.consultant_id' => $ids])->andWhere(['status' => self::NO_FETCHED_STATUS]),
        ]);
        $models = $dataProvider->getModels();
        $response = self::array_copy($models);
        self::changeNoFetchedStatusToFetched($models);
        $dataProvider->models = $response;
        return $dataProvider;
    }
    public static function array_copy($array)
    {
        $newArray = [];
        foreach ($array as $item) {
            $newArray[] = clone $item;
        }
        return $newArray;
    }
    public static function changeNoFetchedStatusToFetched($models)
    {
        foreach ($models as $model) {
            if ($model->status == self::NO_FETCHED_STATUS) {
                $model->status = self::FETCHED_STATUS;
                $model->save();
            }
        }
    }
    public static function changeNoViewedStatusToViewed($models)
    {
        foreach ($models as $model) {
            if ($model->status == self::NO_VIEWED_STATUS && $model->status != self::PROCESSED_STATUS) {
                $model->status = self::VIEWED_STATUS;
                $model->save();
            }
        }
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
}
