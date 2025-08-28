<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\Company\Company;
use app\models\Complex;

class ComplexRepository extends AbstractRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Complex
	{
		return Complex::find()->byId($id)->oneOrThrow();
	}

	public function findOne(int $id): ?Complex
	{
		return Complex::find()->byId($id)->one();
	}

	/**
	 * @return Company[]
	 */
	public function findAll(): array
	{
		return Complex::find()->all();
	}
}