<?php

namespace app\repositories;

use app\kernel\common\repository\ModelRepository;
use app\models\Attribute;

/**
 * @extends ModelRepository<Attribute>
 */
class AttributeRepository extends ModelRepository
{
	protected $className = Attribute::class;

	public function existsByLabel(string $label): bool
	{
		return Attribute::find()->notDeleted()->byLabel($label)->exists();
	}

	public function existsByKind(string $kind): bool
	{
		return Attribute::find()->notDeleted()->byKind($kind)->exists();
	}
}