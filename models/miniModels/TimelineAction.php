<?php

namespace app\models\miniModels;

use app\models\Timeline;
use Yii;

/**
 * This is the model class for table "timeline_action".
 *
 * @property int $id
 * @property int $timeline_id
 * @property int|null $done
 * @property int|null $negative
 * @property int|null $additional
 * @property string|null $date
 *
 * @property FeedbackWay[] $feedbackWays
 * @property Timeline $timeline
 * @property TimelineObject[] $timelineObjects
 */
class TimelineAction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline_action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timeline_id'], 'required'],
            [['timeline_id', 'done', 'negative', 'additional'], 'integer'],
            [['date'], 'safe'],
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
            'done' => 'Done',
            'negative' => 'Negative',
            'additional' => 'Additional',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[FeedbackWays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbackWays()
    {
        return $this->hasMany(FeedbackWay::className(), ['timeline_action_id' => 'id']);
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
     * Gets query for [[TimelineObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineObjects()
    {
        return $this->hasMany(TimelineObject::className(), ['timeline_action_id' => 'id']);
    }
}
