<?php

namespace app\models\miniModels;

use app\models\Timeline;
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
