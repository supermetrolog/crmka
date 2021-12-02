<?php

namespace app\models;

use app\models\miniModels\Phone;
use Yii;
use yii\data\ActiveDataProvider;

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

    public const VIEWED_NOT_REQUESTED = 0;
    public const VIEWED_REQUESTED = 1;
    public const VIEWED_VIEWED = 2;
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
            [['type', 'viewed'], 'integer'],
            [['created_at'], 'safe'],
            [['caller_id', 'from', 'to', 'status', 'uniqueid'], 'string', 'max' => 255],
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

    public static function viewed($id)
    {
        $models = self::find()->joinWith(['caller'])->where(['user_profile.user_id' => $id])->andWhere(['!=', 'viewed', self::VIEWED_VIEWED])->all();

        foreach ($models as $model) {
            if ($model->viewed != self::VIEWED_VIEWED) {
                $model->viewed = self::VIEWED_VIEWED;
                $model->save();
            }
        }
    }
    public static function getCallListForUser($id)
    {
        // $contactColumns = ',contact.middle_name, contact.first_name, contact.company_id, contact.type, contact.position, contact.faceToFaceMeeting, contact.warning, contact.good, contact.status as cstatus';
        // $dataProvider = new ActiveDataProvider([
        //     'query' => self::find()->select('call_list.*, phone.phone, phone.contact_id' . $contactColumns)->joinWith(['caller'])->leftJoin('phone', 'phone.phone = call_list.from')->leftJoin('contact', 'contact.id = phone.contact_id')->where(['user_profile.user_id' => $id])->andWhere(['is not', 'call_list.status', new \yii\db\Expression('null')])->asArray(),
        // ]);
        // return $dataProvider;
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->joinWith(['caller', 'phoneFrom' => function ($query) {
                $query->with(['contact']);
            }, 'phoneTo' => function ($query) {
                $query->with(['contact']);
            }])->where(['user_profile.user_id' => $id])->andWhere(['is not', 'call_list.status', new \yii\db\Expression('null')]),
        ]);
        return $dataProvider;
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