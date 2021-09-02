<?php

namespace app\models\miniModels;

use app\models\Timeline;
use yii\web\NotFoundHttpException;
use app\exceptions\ValidationErrorHttpException;
use ReflectionClass;
use Yii;

/**
 * This is the model class for table "timeline_step".
 *
 * @property int $id
 * @property int $timeline_id [связь] с таймлайном
 * @property int $number номер шага
 * @property string|null $comment общий комментарий к шагу
 * @property int|null $done [флаг] ГОТОВО - используется для любого шага
 * @property int|null $negative [флаг] ОТРИЦАНИЕ - используется для любого шага
 * @property int|null $additional [флаг] ДОПОЛНИТЕЛЬНЫЙ ФЛАГ - используется для любого шага
 * @property string|null $date ДАТА используется для любого шага
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Timeline $timeline
 * @property TimelineStepFeedbackway[] $timelineStepFeedbackways
 * @property TimelineStepObject[] $timelineStepObjects
 */
class TimelineStep extends \yii\db\ActiveRecord
{
    public const MEETING_STEP_NUMBER = 0;
    public const OFFER_STEP_NUMBER = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline_step';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timeline_id', 'number'], 'required'],
            [['timeline_id', 'number', 'done', 'negative', 'additional'], 'integer'],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['timeline_id'], 'exist', 'skipOnError' => true, 'targetClass' => Timeline::className(), 'targetAttribute' => ['timeline_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timeline_id' => 'Timeline ID',
            'number' => 'Number',
            'comment' => 'Comment',
            'done' => 'Done',
            'negative' => 'Negative',
            'additional' => 'Additional',
            'date' => 'Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public static function findModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function createNewStep($stepNumber)
    {
        if (self::find()->where(['timeline_id' => $this->timeline_id])->andWhere(['number' => $stepNumber])->one()) return;
        $model = new TimelineStep();
        $data = [
            'timeline_id' => $this->timeline_id,
            'number' => $stepNumber,
        ];
        if (!$model->load($data, '') || !$model->save()) {
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
    public function updateSpecificStep($post_data)
    {
        switch ($this->number) {
            case self::MEETING_STEP_NUMBER:
                return $this->updateMeetingStep();
                break;
            case self::OFFER_STEP_NUMBER:
                return $this->updateOfferStep($post_data);
                break;
            default:
                throw new NotFoundHttpException('The requested page does not exist.');
                break;
        }
    }
    public function updateMeetingStep()
    {
        if (!$this->done) return;
        return $this->createNewStep(self::OFFER_STEP_NUMBER);
    }
    public function updateOfferStep($post_data)
    {
        return true;
    }
    public static function updateTimelineStep($id, $post_data)
    {
        $timelineStep = self::findModel($id);
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($timelineStep->load($post_data, '') && $timelineStep->save()) {
                $response = $timelineStep->updateSpecificStep($post_data);
                $transaction->commit();
                return ['message' => "Успех", 'data' => true];
            }
            throw new ValidationErrorHttpException($timelineStep->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    /**
     * Gets query for [[Timeline]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeline()
    {
        return $this->hasOne(Timeline::className(), ['id' => 'timeline_id']);
    }

    /**
     * Gets query for [[TimelineStepFeedbackways]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineStepFeedbackways()
    {
        return $this->hasMany(TimelineStepFeedbackway::className(), ['timeline_step_id' => 'id']);
    }

    /**
     * Gets query for [[TimelineStepObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineStepObjects()
    {
        return $this->hasMany(TimelineStepObject::className(), ['timeline_step_id' => 'id']);
    }
}
