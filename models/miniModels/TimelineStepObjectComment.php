<?php

namespace app\models\miniModels;

use Yii;

/**
 * This is the model class for table "timeline_step_object_comment".
 *
 * @property int $id
 * @property int $timeline_step_id [СВЯЗЬ] часть составного внешнего ключа
 * @property int $object_id [СВЯЗЬ] часть составного внешнего ключа
 * @property string $comment комментарий к отправленному или добавленному объекту
 *
 * @property TimelineStepObject $object
 * @property TimelineStepObject $timelineStep
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
            [['timeline_step_id', 'object_id', 'comment'], 'required'],
            [['timeline_step_id', 'object_id'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStepObject::className(), 'targetAttribute' => ['object_id' => 'object_id']],
            [['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStepObject::className(), 'targetAttribute' => ['timeline_step_id' => 'timeline_step_id']],
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
            'object_id' => 'Object ID',
            'comment' => 'Comment',
        ];
    }

    /**
     * Gets query for [[Object]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(TimelineStepObject::className(), ['object_id' => 'object_id']);
    }

    /**
     * Gets query for [[TimelineStep]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineStep()
    {
        return $this->hasOne(TimelineStepObject::className(), ['timeline_step_id' => 'timeline_step_id']);
    }
}
