<?php

namespace app\models;

use Yii;
use app\exceptions\ValidationErrorHttpException;

/**
 * This is the model class for table "calendar".
 *
 * @property int $id
 * @property int $consultant_id [СВЯЗЬ] с юзером
 * @property string $title
 * @property string|null $description
 * @property string $startDate Дата начала события
 * @property string|null $endDate Дата конца события
 * @property int $status [ФЛАГ] статус
 * @property int|null $period_notify Частота уведомлений (раз в час, раз в день и т.д)
 * @property string|null $lastNotifyDate Дата последнего уведомления
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $consultant
 */
class Calendar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calendar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['consultant_id', 'title', 'startDate'], 'required'],
            [['consultant_id', 'status', 'period_notify'], 'integer'],
            [['description'], 'string'],
            [['startDate', 'endDate', 'lastNotifyDate', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
            'status' => 'Status',
            'period_notify' => 'Period Notify',
            'lastNotifyDate' => 'Last Notify Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function createCalendarItem($post_data)
    {
        $db = Yii::$app->db;
        $model = new self();
        $transaction = $db->beginTransaction();
        try {
            if ($model->load($post_data, '') && $model->save()) {
                $transaction->commit();
                return ['message' => "Напоминание создано", 'data' => $model->id];
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
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
