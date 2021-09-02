<?php

namespace app\models\miniModels;

use Yii;

/**
 * This is the model class for table "timeline_step_feedbackway".
 *
 * @property int $id
 * @property int $timeline_step_id [связь] с конкретным шагом таймлайна
 * @property int|null $way Способ получения обратной связи
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TimelineStep $timelineStep
 */
class TimelineStepFeedbackway extends \yii\db\ActiveRecord
{
    public const MAIN_COLUMN = 'way';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline_step_feedbackway';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timeline_step_id'], 'required'],
            [['timeline_step_id', 'way'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStep::className(), 'targetAttribute' => ['timeline_step_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timeline_step_id' => 'Timeline Step ID',
            'way' => 'Way',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[TimelineStep]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineStep()
    {
        return $this->hasOne(TimelineStep::className(), ['id' => 'timeline_step_id']);
    }
}
