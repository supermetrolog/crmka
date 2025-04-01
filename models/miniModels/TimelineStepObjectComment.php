<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\TimelineQuery;
use app\models\Timeline;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "timeline_step_object_comment".
 *
 * @property int                $id
 * @property int                $timeline_step_id        [СВЯЗЬ] часть составного внешнего ключа
 * @property string             $comment                 комментарий к отправленному или добавленному объекту
 * @property int                $offer_id                [СВЯЗЬ] с original_id в c_industry_offers_mix
 * @property int                $type_id                 [СВЯЗЬ] с type_id в c_industry_offers_mix
 * @property int                $object_id               [СВЯЗЬ] с object_id в c_industry_offers_mix
 * @property int                $timeline_step_object_id [СВЯЗЬ] с timeline_step_object
 * @property int                $timeline_id             [СВЯЗЬ] с timeline
 *
 * @property TimelineStepObject $timelineStep
 * @property TimelineStepObject $timelineStepObject
 */
class TimelineStepObjectComment extends AR
{
	public static function tableName(): string
	{
		return 'timeline_step_object_comment';
	}

	public function rules(): array
	{
		return [
			[['timeline_step_id', 'comment', 'offer_id', 'type_id', 'object_id', 'timeline_step_object_id'], 'required'],
			[['timeline_id', 'timeline_step_id', 'offer_id', 'type_id', 'object_id', 'timeline_step_object_id'], 'integer'],
			[['comment'], 'string', 'max' => 255],
			[['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStepObject::class, 'targetAttribute' => ['timeline_step_id' => 'timeline_step_id']],
			[['timeline_step_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStepObject::class, 'targetAttribute' => ['timeline_step_object_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'                      => 'ID',
			'timeline_step_id'        => 'Timeline Step ID',
			'comment'                 => 'Comment',
			'offer_id'                => 'Offer ID',
			'type_id'                 => 'Type ID',
			'object_id'               => 'Object ID',
			'timeline_step_object_id' => 'Timeline Step Object ID',
			'timeline_id'             => 'Timeline ID',
		];
	}

	public function getTimelineStep(): ActiveQuery
	{
		return $this->hasOne(TimelineStep::class, ['id' => 'timeline_step_id']);
	}

	public function getTimeline(): TimelineQuery
	{
		/** @var TimelineQuery */
		return $this->hasOne(Timeline::class, ['id' => 'timeline_id']);
	}

	public function getTimelineStepObject(): ActiveQuery
	{
		return $this->hasOne(TimelineStepObject::class, ['id' => 'timeline_step_object_id']);
	}
}
