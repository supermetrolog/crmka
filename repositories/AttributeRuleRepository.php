<?php

namespace app\repositories;

use app\kernel\common\repository\ModelRepository;
use app\models\AttributeRule;

/**
 * @extends ModelRepository<AttributeRule>
 */
class AttributeRuleRepository extends ModelRepository
{
	protected string $className = AttributeRule::class;
}