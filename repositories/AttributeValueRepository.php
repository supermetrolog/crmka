<?php

namespace app\repositories;

use app\kernel\common\repository\ModelRepository;
use app\models\AttributeValue;

/**
 * @extends ModelRepository<AttributeValue>
 */
class AttributeValueRepository extends ModelRepository
{
	protected string $className = AttributeValue::class;
}