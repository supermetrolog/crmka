<?php

declare(strict_types=1);

namespace app\usecases\Timeline;

use app\dto\Timeline\TimelineStepObjectDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\TimelineStep;
use app\models\miniModels\TimelineStepObject;
use app\models\miniModels\TimelineStepObjectComment;
use Throwable;

class TimelineStepObjectService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(TimelineStep $step, TimelineStepObjectDto $dto): TimelineStepObject
	{
		$object = new TimelineStepObject([
			'timeline_step_id' => $step->id,
			'object_id'        => $dto->object_id,
			'status'           => $dto->status,
			'option'           => $dto->option,
			'type_id'          => $dto->type_id,
			'offer_id'         => $dto->offer_id,
			'comment'          => $dto->comment,

			'complex_id'     => $dto->offerMix->complex_id,
			'class_name'     => $dto->offerMix->class_name,
			'deal_type_name' => $dto->offerMix->deal_type_name,
			'visual_id'      => $dto->offerMix->visual_id,
			'address'        => $dto->offerMix->address,
			'area'           => $dto->offerMix->getCalcAreaGeneral(),
			'price'          => $dto->offerMix->getCalcPrice(),
			'image'          => $dto->offerMix->getThumb()
		]);

		$object->saveOrThrow();

		return $object;
	}

	/**
	 * @throws SaveModelException
	 */
	public function createComment(TimelineStepObject $stepObject): TimelineStepObjectComment
	{
		$comment = new TimelineStepObjectComment([
			'timeline_id'             => $stepObject->timeline_id,
			'timeline_step_id'        => $stepObject->timeline_step_id,
			'timeline_step_object_id' => $stepObject->id,
			'object_id'               => $stepObject->object_id,
			'type_id'                 => $stepObject->type_id,
			'offer_id'                => $stepObject->offer_id,
			'comment'                 => $stepObject->comment,
		]);

		$comment->saveOrThrow();

		return $comment;
	}

	public function deleteAllByStep(TimelineStep $step): void
	{
		TimelineStepObject::deleteAll(['timeline_step_id' => $step->id]);
	}
}