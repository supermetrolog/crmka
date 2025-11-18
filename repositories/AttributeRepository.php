<?php

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Attribute;

class AttributeRepository
{
	// TODO: attribute

	public function existsByLabel(string $label): bool
	{
		return Attribute::find()->notDeleted()->byLabel($label)->exists();
	}

	public function existsByKind(string $kind): bool
	{
		return Attribute::find()->notDeleted()->byKind($kind)->exists();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Attribute
	{
		return Attribute::find()->notDeleted()->byId($id)->oneOrThrow();
	}
}