<?php

namespace app\models\miniModels;

use Yii;

/**
 * This is the model class for table "timeline_object".
 *
 * @property int $id
 * @property int $timeline_action_id
 * @property int $object_id
 * @property int|null $type
 *
 * @property TimelineAction $timelineAction
 */
class TimelineObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timeline_action_id', 'object_id'], 'required'],
            [['timeline_action_id', 'object_id', 'type'], 'integer'],
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
            'object_id' => 'Object ID',
            'type' => 'Type',
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
