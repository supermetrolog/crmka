<?php

namespace app\dto\Attribute;

use yii\base\BaseObject;

class CreateAttributeDto extends BaseObject
{
	public string $kind;
	public string $label;
	public string $description;
	public string $value_type;
	public string $input_type;
}