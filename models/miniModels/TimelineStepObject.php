<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\oldDb\OfferMixQuery;
use app\models\oldDb\OfferMix;
use yii\db\ActiveQuery;

/**
 * @property int                              $id
 * @property int                              $timeline_step_id [связь] с конкретным шагом таймлайна
 * @property int                              $object_id        ID объекта
 * @property ?int                             $status
 * @property ?int                             $option           Дополнительные флаги для объекта
 * @property ?string                          $created_at
 * @property ?string                          $updated_at
 * @property ?int                             $type_id          Херня для API
 * @property int                              $offer_id         Нужен для поиска сразу нескольких предложений по API
 * @property ?int                             $complex_id
 * @property ?string                          $comment          комментарий к объекту
 * @property ?int                             $timeline_id
 * @property ?string                          $class_name
 * @property ?string                          $deal_type_name
 * @property ?string                          $visual_id
 * @property ?string                          $address
 * @property ?string                          $area
 * @property ?string                          $price
 * @property ?string                          $image
 *
 * @property int                              $duplicate_count
 *
 * @property-read TimelineStep                $timelineStep
 * @property-read TimelineStepObjectComment[] $comments
 * @property-read OfferMix                    $offer
 */
class TimelineStepObject extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;

	public int $duplicate_count = 1;

	public static function tableName(): string
	{
		return 'timeline_step_object';
	}

	public function rules(): array
	{
		return [
			[['timeline_step_id', 'object_id', 'offer_id'], 'required'],
			[['timeline_id', 'timeline_step_id', 'object_id', 'status', 'option', 'type_id', 'offer_id', 'complex_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['image', 'price', 'area', 'address', 'visual_id', 'deal_type_name', 'class_name', 'comment'], 'string', 'max' => 255],
			[['timeline_step_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimelineStep::class, 'targetAttribute' => ['timeline_step_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'               => 'ID',
			'timeline_step_id' => 'Timeline Step ID',
			'object_id'        => 'Object ID',
			'status'           => 'Status',
			'option'           => 'Option',
			'created_at'       => 'Created At',
			'updated_at'       => 'Updated At',
			'type_id'          => 'Type ID',
			'offer_id'         => 'Offer ID',
			'complex_id'       => 'Complex ID',
			'comment'          => 'Comment',
			'class_name'       => 'Class Name',
			'deal_type_name'   => 'Deal Type Name',
			'visual_id'        => 'Visual ID',
			'address'          => 'Address',
			'area'             => 'Area',
			'price'            => 'Price',
			'image'            => 'Image',
		];
	}

	public function getTimelineStep(): ActiveQuery
	{
		return $this->hasOne(TimelineStep::class, ['id' => 'timeline_step_id']);
	}

	public function getOffer(): OfferMixQuery
	{
		/** @var OfferMixQuery */
		return $this->hasOne(OfferMix::class, [
			'object_id'   => 'object_id',
			'type_id'     => 'type_id',
			'original_id' => 'offer_id'
		]);
	}

	public function getComments(): ActiveQuery
	{
		return $this->hasMany(TimelineStepObjectComment::class, [
			'timeline_id' => 'timeline_id',
			'offer_id'    => 'offer_id',
			'type_id'     => 'type_id'
		]);
	}

	public function hasComment(): bool
	{
		return !empty($this->comment);
	}

	public function setDuplicateCount(int $count): void
	{
		$this->duplicate_count = $count;
	}
}
