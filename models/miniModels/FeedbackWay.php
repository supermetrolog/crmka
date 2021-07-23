<?php

namespace app\models\miniModels;

use Yii;

/**
 * This is the model class for table "feedback_way".
 *
 * @property int $id
 * @property int $timeline_action_id
 * @property int $way
 *
 * @property TimelineAction $timelineAction
 */
class FeedbackWay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feedback_way';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timeline_action_id', 'way'], 'required'],
            [['timeline_action_id', 'way'], 'integer'],
            [['timeline_action_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineAction::className(), 'targetAttribute' => ['timeline_action_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timeline_action_id' => 'Timeline Action ID',
            'way' => 'Way',
        ];
    }

    /**
     * Gets query for [[TimelineAction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineAction()
    {
        return $this->hasOne(TimelineAction::className(), ['id' => 'timeline_action_id']);
    }
}
