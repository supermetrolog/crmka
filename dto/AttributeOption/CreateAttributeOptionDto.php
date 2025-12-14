<?php

namespace app\dto\AttributeOption;

use yii\base\BaseObject;

class CreateAttributeOptionDto extends BaseObject
{
	public int     $attributeId;
	public string  $value;
	public ?string $label;
	public ?int    $sortOrder;
}