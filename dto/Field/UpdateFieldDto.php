<?php

declare(strict_types=1);

namespace app\dto\Field;

use yii\base\BaseObject;

class UpdateFieldDto extends BaseObject
{
	public int $field_type;
	public int $type;
}