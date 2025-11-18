<?php

namespace app\dto\AttributeOption;

use yii\base\BaseObject;

class UpdateAttributeOptionDto extends BaseObject
{
	public string  $value;
	public ?string $label;
	public ?int    $sort_order;
}