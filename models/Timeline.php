<?php

namespace app\models;

use app\exceptions\ValidationErrorHttpException;
use app\models\ActiveQuery\TimelineQuery;
use app\models\miniModels\TimelineActionComment;
use app\models\miniModels\TimelineStep;
use LogicException;
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
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public const STATUS_INACTIVE_WHEN_TIMEOUT = -1;
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
        $data = [];
        $data['timeline'] = self::find()->with(['timelineSteps' => function ($query) {
            $query->with(['timelineStepObjects' => function ($query) {
                $query->with(
                    'comments',
                    'offer.object',
                    'offer.comments',
                    'offer.generalOffersMix.offer'
                );
            }, 'timelineStepFeedbackways', 'timelineActionComments']);
        }, 'timelineActionComments' => function ($query) {
            $query->orderBy(['timeline_action_comment.created_at' => SORT_DESC]);
        }])->where(['timeline.request_id' => $request_id])->andWhere(['timeline.consultant_id' => $consultant_id])->limit(1)->one();

        $data['timelineList'] = self::getTimelineListInRequest($request_id);
        return $data;
    }
    public static function getTimelineListInRequest($request_id)
    {
        return self::find()->joinWith(['consultant.userProfile'])->where(['timeline.request_id' => $request_id])->orderBy(['status' => SORT_ASC])->all();
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
    public static function updateConsultant($request_id, $consultant_id)
    {
        $models = self::find()->where(['request_id' => $request_id])->all();
        foreach ($models as  $model) {
            $model->status = self::STATUS_INACTIVE;
            if (!$model->save()) {
                throw new LogicException(json_decode($model->getErrorSummary(false)));
            }
        }

        $model = self::find()->where(['request_id' => $request_id])->andWhere(['consultant_id' => $consultant_id])->one();
        if ($model) {
            $model->status = self::STATUS_ACTIVE;
            $model->save();
            return true;
        }
        return self::createNewTimeline($request_id, $consultant_id);
    }

    public static function getActiveTimelineForRequest(int $request_id): ?self
    {
        return self::find()->where(['request_id' => $request_id, 'status' => self::STATUS_ACTIVE])->limit(1)->one();
    }
    public static function getTimelineByRequestAndConsultantID(int $request_id, int $consultant_id)
    {
        return self::find()->where(['request_id' => $request_id, 'consultant_id' => $consultant_id])->limit(1)->one();
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

	/**
	 * Gets query for [[TimelineSteps]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getTimelineActionComments()
	{
		return $this->hasMany(TimelineActionComment::className(), ['timeline_id' => 'id']);
	}

	public static function find(): TimelineQuery
	{
		return new TimelineQuery(get_called_class());
	}
}
