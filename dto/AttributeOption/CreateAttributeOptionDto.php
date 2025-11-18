<?php

namespace app\dto\AttributeOption;

use yii\base\BaseObject;

class CreateAttributeOptionDto extends BaseObject
{
	public int     $attribute_id;
	public string  $value;
	public ?string $label;
	public ?int    $sort_order;
}