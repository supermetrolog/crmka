<?php

declare(strict_types=1);

namespace app\models\forms\Timeline;

use app\dto\Timeline\TimelineStepObjectDto;
use app\kernel\common\models\Form\Form;
use app\models\OfferMix;

class TimelineStepObjectForm extends Form
{
	public $object_id;
	public $offer_id;
	public $status;
	public $option;
	public $type_id;
	public $comment;

	public function rules(): array
	{
		return [
			[['object_id', 'offer_id', 'type_id'], 'required'],
			[['object_id', 'status', 'option', 'offer_id'], 'integer'],
			[['comment'], 'string', 'max' => 255],
			['offer_id', 'exist', 'skipOnError' => true, 'targetClass' => OfferMix::class, 'targetAttribute' => ['offer_id' => 'original_id', 'object_id' => 'object_id', 'type_id' => 'type_id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'object_id' => 'ID объекта',
			'status'    => 'Статус',
			'option'    => 'Дополнительная опция',
			'type_id'   => 'Тип предложения',
			'offer_id'  => 'ID объекта',
			'comment'   => 'Комментарий'
		];
	}

	private function findOfferMix(): OfferMix
	{
		/** @var OfferMix */
		return OfferMix::find()->byOriginalId($this->offer_id)->byType($this->type_id)->byObjectId($this->object_id)->one();
	}

	public function getDto(): TimelineStepObjectDto
	{
		return new TimelineStepObjectDto([
			'object_id' => $this->object_id,
			'option'    => $this->option,
			'type_id'   => $this->type_id,
			'offer_id'  => $this->offer_id,
			'comment'   => $this->comment,

			/** TODO: Зачем это? Во всей таблице null. Это дубль из offerMix? */
			'status'    => $this->status,

			'offerMix' => $this->findOfferMix()
		]);
	}
}