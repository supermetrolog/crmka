<?php

namespace app\models\miniModels;

use Yii;

/**
 * This is the model class for table "timeline_step_object".
 *
 * @property int $id
 * @property int $timeline_step_id [связь] с конкретным шагом таймлайна
 * @property int $object_id ID объекта
 * @property int $offer_id ID предложения
 * @property int $complex_id ID комплекса
 * @property int|null $status
 * @property int|null $option Дополнительные флаги для объекта
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $comment
 * @property int|null $type_id Херня для API
 *
 * @property TimelineStep $timelineStep
 */
class TimelineStepObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline_step_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timeline_step_id', 'object_id', 'offer_id'], 'required'],
            [['timeline_step_id', 'object_id', 'offer_id', 'status', 'option', 'type_id', 'complex_id'], 'integer'],
            [['comment'], 'string'],
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
            'object_id' => 'Object ID',
            'offer_id' => 'Offer ID',
            'complex_id' => 'Complex ID',
            'status' => 'Status',
            'option' => 'Option',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'comment' => 'Comment',
            'type_id' => 'Type ID',
        ];
    }
    public static function addObjects($id, $post_data)
    {
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
