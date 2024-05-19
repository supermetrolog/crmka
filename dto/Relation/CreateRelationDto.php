<?php

declare(strict_types=1);

namespace app\dto\Relation;

use yii\base\BaseObject;

class CreateRelationDto extends BaseObject
{
	public string $first_type;
	public int    $first_id;
	public string $second_type;
	public int    $second_id;
}