<?php

namespace app\dto\Attribute;

use yii\base\BaseObject;

class UpdateAttributeOptionDto extends BaseObject
{
	public string  $value;
	public ?string $label;
	public ?int    $sortOrder;
}