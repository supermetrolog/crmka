<?php

declare(strict_types=1);

namespace app\usecases\Relation;

use app\dto\Relation\CreateRelationDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Relation;

class RelationService
{

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateRelationDto $dto): Relation
	{
		$model = new Relation();

		$model->first_type  = $dto->first_type;
		$model->first_id    = $dto->first_id;
		$model->second_type = $dto->second_type;
		$model->second_id   = $dto->second_id;

		$model->saveOrThrow();

		return $model;
	}
}