<?php

namespace app\models;

use app\models\miniModels\Phone;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * This is the model class for table "call_list".
 *
 * @property int $id
 * @property string $caller_id [связь] с user_profile (номер в системе Asterisk)
 * @property string $from кто звонит
 * @property string $to кому звонят
 * @property int $type [флаг] тип звонка (0 - исходящий / 1 - входящий
 * @property string|null $created_at
 * @property string|null $status чем закончился звонок
 * @property string|null $uniqueid realtime call unique ID
 * @property int|null $viewed [флаг] 0 - не запрошено, 1 - было запрошено, 2 - просмотренно в уведомлениях
 *
 * @property UserProfile $caller
 */
class CallList extends \yii\db\ActiveRecord
{
    public const TYPE_OUTGOING = 0;
    public const TYPE_INCOMING = 1;

    public const FETCHED_STATUS = 0;
    public const NO_FETCHED_STATUS = -1;
    public const VIEWED_STATUS = 1;
    public const NO_VIEWED_STATUS = 0;
    public const PROCESSED_STATUS = 2;
    public const NO_COUNT_STATUS = 3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['caller_id', 'from', 'to', 'type'], 'required'],
            [['type', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['caller_id', 'from', 'to', 'call_ended_status', 'uniqueid'], 'string', 'max' => 255],
            [['caller_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserProfile::className(), 'targetAttribute' => ['caller_id' => 'caller_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'caller_id' => 'Caller ID',
            'from' => 'From',
            'to' => 'To',
            'type' => 'Type',
            'created_at' => 'Created At',
            'status' => 'Status',
            'uniqueid' => 'Uniqueid',
            'viewed' => 'Viewed',
        ];
    }

    public static function viewedNotCount($caller_id)
    {
        $models = self::find()->where(['caller_id' => $caller_id])->andWhere(['status' => self::NO_COUNT_STATUS])->all();
        foreach ($models as $model) {
            $model->status = self::VIEWED_STATUS;
            $model->save();
        }
    }
    public static function viewedAll($caller_id)
    {
        $models = self::find()->where(['caller_id' => $caller_id])->andWhere(['status' => [self::NO_COUNT_STATUS, self::NO_VIEWED_STATUS]])->all();
        foreach ($models as $model) {
            $model->status = self::VIEWED_STATUS;
            $model->save();
        }
    }
    public static function getCallsCount($caller_id)
    {
        // return self::find()->where(['caller_id' => $caller_id, 'status' => [self::NO_FETCHED_STATUS, self::NO_VIEWED_STATUS]])->andWhere(['is not', 'call_ended_status', new Expression('null')])->count();
        return self::find()->where(['caller_id' => $caller_id, 'status' => [self::NO_FETCHED_STATUS, self::NO_VIEWED_STATUS]])->count();
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
    public static function changeNoViewedStatusToNoCount($models)
    {
        foreach ($models as $model) {
            if ($model->status == self::NO_VIEWED_STATUS && $model->status != self::PROCESSED_STATUS) {
                $model->status = self::NO_COUNT_STATUS;
                $model->save();
            }
        }
    }
    /**
     * Gets query for [[Caller]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCaller()
    {
        return $this->hasOne(UserProfile::className(), ['caller_id' => 'caller_id']);
    }
    public function getPhoneFrom()
    {
        return $this->hasOne(Phone::className(), ['phone' => 'from']);
    }
    public function getPhoneTo()
    {
        return $this->hasOne(Phone::className(), ['phone' => 'to']);
    }
}
