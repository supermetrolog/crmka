<?php

namespace app\repositories;

use app\kernel\common\repository\ModelRepository;
use app\models\AttributeOption;

/**
 * @extends ModelRepository<AttributeOption>
 */
class AttributeOptionRepository extends ModelRepository
{
	protected string $className = AttributeOption::class;
}