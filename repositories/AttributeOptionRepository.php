<?php

namespace app\repositories;

use app\kernel\common\repository\ModelRepository;
use app\models\AttributeOption;

/**
 * @extends ModelRepository<AttributeOption>
 */
class AttributeOptionRepository extends ModelRepository
{
	protected $className = AttributeOption::class;
}