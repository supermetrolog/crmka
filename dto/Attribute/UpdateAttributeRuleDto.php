<?php

namespace app\dto\Attribute;

use yii\base\BaseObject;

class UpdateAttributeRuleDto extends BaseObject
{
	public ?int   $attributeGroupId;
	public string $entityType;
	public bool   $isRequired;
	public bool   $isInheritable;
	public bool   $isEditable;
	public string $status;
	public int    $sortOrder;
}