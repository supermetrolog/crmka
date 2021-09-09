<?php

namespace app\models;

use app\models\miniModels\TimelineStep;
use yii\data\ActiveDataProvider;
use app\exceptions\ValidationErrorHttpException;
use Yii;

/**
 * This is the model class for table "timeline".
 *
 * @property int $id
 * @property int $request_id [связь] с запросами
 * @property int $consultant_id [связь] с юзерами
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User $consultant
 * @property Request $request
 * @property TimelineStep[] $timelineSteps
 */
class Timeline extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'consultant_id'], 'required'],
            [['request_id', 'consultant_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
            'request_id' => 'Request ID',
            'consultant_id' => 'Consultant ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getTimeline($consultant_id, $request_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->joinWith(['timelineSteps' => function ($query) {
                $query->joinWith(['timelineStepObjects', 'timelineStepFeedbackways']);
            }])->where(['timeline.request_id' => $request_id])->andWhere(['timeline.consultant_id' => $consultant_id]),
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $dataProvider;
    }
    public static function createNewTimeline($request_id, $consultant_id)
    {
        $data = [
            'request_id' => $request_id,
            'consultant_id' => $consultant_id,
        ];
        $timeline = new Timeline();
        $timelineStep = new TimelineStep();
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($timeline->load($data, '') && $timeline->save()) {
                $data = [
                    'timeline_id' => $timeline->id,
                    'number' => TimelineStep::MEETING_STEP_NUMBER,
                ];
                if (!$timelineStep->load($data, '') || !$timelineStep->save()) {
                    throw new ValidationErrorHttpException($timelineStep->getErrorSummary(false));
                }
            } else {
                throw new ValidationErrorHttpException($timeline->getErrorSummary(false));
            }
            $transaction->commit();
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

    /**
     * Gets query for [[Request]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequest()
    {
        return $this->hasOne(Request::className(), ['id' => 'request_id']);
    }

    /**
     * Gets query for [[TimelineSteps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineSteps()
    {
        return $this->hasMany(TimelineStep::className(), ['timeline_id' => 'id']);
    }
}
