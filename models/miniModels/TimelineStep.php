<?php

namespace app\models\miniModels;

use app\models\Timeline;
use yii\web\NotFoundHttpException;
use app\exceptions\ValidationErrorHttpException;
use app\models\Deal;
use app\models\Request;
use Yii;
use yii\helpers\ArrayHelper;

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
 * @property int|null $status [флаг]
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
    public const FEEDBACK_STEP_NUMBER = 2;
    public const INSPECTION_STEP_NUMBER = 3;
    public const VISIT_STEP_NUMBER = 4;
    public const INTEREST_STEP_NUMBER = 5;
    public const TALK_STEP_NUMBER = 6;
    public const DEAL_STEP_NUMBER = 7;
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
            [['timeline_id', 'number', 'done', 'negative', 'additional', 'status'], 'integer'],
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
    // Сравнение массивов
    private function hasTheArrayChanged($array1, $array2)
    {
        $count = [];
        foreach ($array1 as $item) {
            if (!key_exists($item, $count)) {
                $count[$item] = 1;
            } else {
                $count[$item]++;
            }
        }
        foreach ($array2 as $item) {
            if (!key_exists($item, $count)) {
                $count[$item] = 1;
            } else {
                $count[$item]++;
            }
        }
        foreach ($count as $item) {
            if ($item == 1) {
                return true;
            }
        }
        return false;
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
            case self::FEEDBACK_STEP_NUMBER:
                return $this->updateFeedbackStep($post_data);
                break;
            case self::INSPECTION_STEP_NUMBER:
                return $this->updateInspectionStep($post_data);
                break;
            case self::VISIT_STEP_NUMBER:
                return $this->updateVisitStep($post_data);
                break;
            case self::INTEREST_STEP_NUMBER:
                return $this->updateInterestStep($post_data);
                break;
            case self::TALK_STEP_NUMBER:
                return $this->updateTalkStep($post_data);
                break;
            case self::DEAL_STEP_NUMBER:
                return $this->updateDealStep($post_data);
                break;
            default:
                throw new NotFoundHttpException('The requested page does not exist.');
                break;
        }
    }
    private function compareValueAndValueInArray($objects, $newObjectObjectId)
    {
        foreach ($objects as $object) {
            if ($object->object_id == $newObjectObjectId) {
                return true;
            }
        }
        return false;
    }
    private function addTimelineStepObjects($post_data, $noDuplicate = false, $deleteAllBeforeAdd = true)
    {
        $newObjects = $post_data['timelineStepObjects'];
        if ($deleteAllBeforeAdd) {
            TimelineStepObject::deleteAll(['timeline_step_id' => $this->id]);
            $noDuplicate = false;
        }
        if ($noDuplicate) {
            $currentObjects = TimelineStepObject::find()->where(['timeline_step_id' => $this->id])->all();
        }
        foreach ($newObjects as $object) {
            if ($noDuplicate && $this->compareValueAndValueInArray($currentObjects, $object['object_id'])) {
                continue;
            }

            $object['updated_at'] = date('Y-m-d H:i:s');
            $model = new TimelineStepObject();
            if (!$model->load($object, '') || !$model->save()) {
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
            }
            if (!$model->comment) {
                continue;
            }
            $commentModel = new TimelineStepObjectComment([
                'timeline_step_id' => $model->timeline_step_id,
                'object_id' => $model->object_id,
                'comment' => $model->comment,
            ]);
            if (!$commentModel->save()) {
                throw new ValidationErrorHttpException($commentModel->getErrorSummary(false));
            }
        }
    }
    public function updateMeetingStep()
    {
        if (!$this->done) return;
        return $this->createNewStep(self::OFFER_STEP_NUMBER);
    }
    public function updateOfferStep($post_data)
    {
        if ($this->negative) return;
        $this->addTimelineStepObjects($post_data, false, false);
        return $this->createNewStep(self::FEEDBACK_STEP_NUMBER);
    }
    public function updateFeedbackStep($post_data)
    {
        $currentFeedbackways = TimelineStepFeedbackway::find()->where(['timeline_step_id' => $this->id])->asArray()->all();
        $array1 = ArrayHelper::getColumn($currentFeedbackways, 'way');
        $array2 = ArrayHelper::getColumn($post_data['timelineStepFeedbackways'], 'way');
        $hasTheArrayChangedFlag = $this->hasTheArrayChanged($array1, $array2);
        if (!$this->negative && !$hasTheArrayChangedFlag) {
            $this->addTimelineStepObjects($post_data, false, false);
            $this->createNewStep(self::INSPECTION_STEP_NUMBER);
        }

        if ($hasTheArrayChangedFlag) {
            TimelineStepFeedbackway::deleteAll(['timeline_step_id' => $this->id]);

            $newFeedbackWays = $post_data['timelineStepFeedbackways'];
            foreach ($newFeedbackWays as $way) {
                $model = new TimelineStepFeedbackway();
                if (!$model->load($way, '') || !$model->save()) {
                    throw new ValidationErrorHttpException($model->getErrorSummary(false));
                }
            }
        }
    }
    public function updateInspectionStep($post_data)
    {
        if ($this->negative) return;
        $this->addTimelineStepObjects($post_data, false, false);
        return $this->createNewStep(self::VISIT_STEP_NUMBER);
    }
    public function updateVisitStep($post_data)
    {
        if ($this->negative) return;
        $this->addTimelineStepObjects($post_data, false, false);
        return $this->createNewStep(self::INTEREST_STEP_NUMBER);
    }
    public function updateInterestStep($post_data)
    {
        if ($this->negative) return;
        $this->addTimelineStepObjects($post_data, false, false);
        return $this->createNewStep(self::TALK_STEP_NUMBER);
    }
    public function updateTalkStep($post_data)
    {
        if ($this->negative) return;
        $this->addTimelineStepObjects($post_data, false, false);
        return $this->createNewStep(self::DEAL_STEP_NUMBER);
    }
    public function updateDealStep($post_data)
    {
        if ($this->negative) return;

        if (ArrayHelper::keyExists('deal', $post_data)) {
            Request::changeStatus($post_data['deal']['request_id'], Request::STATUS_DONE);
        } else {
            if ($dealModel = $this->timeline->request->deal) {
                $dealModel->object_id =  $post_data['timelineStepObjects'][0]['object_id'];
                $dealModel->complex_id =  $post_data['timelineStepObjects'][0]['complex_id'];
                if (!$dealModel->save()) {
                    throw new ValidationErrorHttpException($dealModel->getErrorSummary(false));
                }
            }
            $this->addTimelineStepObjects($post_data);
        }
    }
    public static function updateTimelineStep($id, $post_data)
    {
        $timelineStep = self::findModel($id);
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($timelineStep->load($post_data, '') && $timelineStep->save()) {
                $timelineStep->updateSpecificStep($post_data);
                TimelineActionComment::addActionComments($post_data);
                $transaction->commit();
                return ['message' => "Успешно изменено", 'data' => true];
            }
            throw new ValidationErrorHttpException($timelineStep->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public function extraFields()
    {
        $extraFields = parent::extraFields();
        $extraFields['timelineStepObjects'] = function ($extraFields) {
            $count = array_count_values((array_map(function ($item) {
                return $item['object_id'];
            }, $extraFields['timelineStepObjects'])));
            $newObjects = [];
            foreach ($extraFields['timelineStepObjects'] as $value) {
                $object = $value->attributes;
                $object['comments'] = $value->comments;
                $object['duplicate_count'] = $count[$object['object_id']];
                $newObjects[$object['object_id']] = $object;
            }
            $fuck = [];
            foreach ($newObjects as $value) {
                $fuck[] = $value;
            }
            return $fuck;
        };
        return $extraFields;
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
    /**
     * Gets query for [[TimelineActionComment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineActionComments()
    {
        return $this->hasMany(TimelineActionComment::className(), ['timeline_step_id' => 'id']);
    }
}
