<?php

namespace app\dto\Attribute;

use yii\base\BaseObject;

class UpdateAttributeDto extends BaseObject
{
	public string  $label;
	public ?string $description;
	public string  $valueType;
	public string  $inputType;
}