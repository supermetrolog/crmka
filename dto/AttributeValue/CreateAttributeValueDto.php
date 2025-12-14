<?php

namespace app\dto\AttributeValue;

use yii\base\BaseObject;

class CreateAttributeValueDto extends BaseObject
{
	public int     $attributeId;
	public int     $entityId;
	public string  $entityType;
	public ?string $value;
}