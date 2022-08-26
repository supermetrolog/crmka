<?php

namespace app\models\miniModels;

use app\models\Timeline;
use Yii;

/**
 * This is the model class for table "timeline_step_object_comment".
 *
 * @property int $id
 * @property int $timeline_step_id [СВЯЗЬ] часть составного внешнего ключа
 * @property string $comment комментарий к отправленному или добавленному объекту
 * @property int $offer_id [СВЯЗЬ] с original_id в c_industry_offers_mix
 * @property int $type_id [СВЯЗЬ] с type_id в c_industry_offers_mix
 * @property int $object_id [СВЯЗЬ] с object_id в c_industry_offers_mix
 * @property int $timeline_step_object_id [СВЯЗЬ] с timeline_step_object
 * @property int $timeline_id [СВЯЗЬ] с timeline
 *
 * @property TimelineStepObject $timelineStep
 * @property TimelineStepObject $timelineStepObject
 */
class TimelineStepObjectComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline_step_object_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timeline_step_id', 'comment', 'offer_id', 'type_id', 'object_id', 'timeline_step_object_id'], 'required'],
            [['timeline_id', 'timeline_step_id', 'offer_id', 'type_id', 'object_id', 'timeline_step_object_id'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStepObject::className(), 'targetAttribute' => ['timeline_step_id' => 'timeline_step_id']],
            [['timeline_step_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStepObject::className(), 'targetAttribute' => ['timeline_step_object_id' => 'id']],
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
            'comment' => 'Comment',
            'offer_id' => 'Offer ID',
            'type_id' => 'Type ID',
            'object_id' => 'Object ID',
            'timeline_step_object_id' => 'Timeline Step Object ID',
            'timeline_id' => 'Timeline ID',
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
     * Gets query for [[TimelineStepObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineStepObject()
    {
        return $this->hasOne(TimelineStepObject::className(), ['id' => 'timeline_step_object_id']);
    }
}
