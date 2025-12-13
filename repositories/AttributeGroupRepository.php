<?php

namespace app\repositories;

use app\kernel\common\repository\ModelRepository;
use app\models\AttributeGroup;

/**
 * @extends ModelRepository<AttributeGroup>
 */
class AttributeGroupRepository extends ModelRepository
{
	protected string $className = AttributeGroup::class;
}