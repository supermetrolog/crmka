<?php

namespace app\models\miniModels;

use app\models\oldDb\OfferMix;
use Yii;

/**
 * This is the model class for table "timeline_step_object".
 *
 * @property int $id
 * @property int $timeline_step_id [связь] с конкретным шагом таймлайна
 * @property int $object_id ID объекта
 * @property int|null $status
 * @property int|null $option Дополнительные флаги для объекта
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $type_id Херня для API
 * @property int $offer_id Нужен для поиска сразу нескольких предложений по API
 * @property int|null $complex_id
 * @property string|null $comment комментарий к объекту
 *
 * @property TimelineStep $timelineStep
 * @property TimelineStepObjectComment[] $timelineStepObjectComments
 * @property TimelineStepObjectComment[] $timelineStepObjectComments0
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
            [['timeline_id', 'timeline_step_id', 'object_id', 'status', 'option', 'type_id', 'offer_id', 'complex_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['image', 'price', 'area', 'address', 'visual_id', 'deal_type_name', 'class_name', 'comment'], 'string', 'max' => 255],
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
            'status' => 'Status',
            'option' => 'Option',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'type_id' => 'Type ID',
            'offer_id' => 'Offer ID',
            'complex_id' => 'Complex ID',
            'comment' => 'Comment',
            'class_name' => 'Class Name',
            'deal_type_name' => 'Deal Type Name',
            'visual_id' => 'Visual ID',
            'address' => 'Address',
            'area' => 'Area',
            'price' => 'Price',
            'image' => 'Image',
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
     * Gets query for [[Offers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne(OfferMix::className(), ['object_id' => 'object_id', 'type_id' => 'type_id', 'original_id' => 'offer_id']);
    }
    // /**
    //  * Gets query for [[Comments]].
    //  *
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getComments()
    // {
    //     return $this->hasMany(TimelineStepObjectComment::className(), ['timeline_step_object_id' => 'id']);
    // }
    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(TimelineStepObjectComment::className(), ['timeline_id' => 'timeline_id', 'offer_id' => 'offer_id', 'type_id' => 'type_id']);
    }
    /**
     * Gets query for [[getTimelineStepObjectCommentsWithTimelineStepId]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimelineStepObjectCommentsWithTimelineStepId()
    {
        return $this->hasMany(TimelineStepObjectComment::className(), ['timeline_step_id' => 'timeline_step_id']);
    }
}
