<?php

namespace app\dto\ContactPosition;

use yii\base\BaseObject;

class UpdateContactPositionDto extends BaseObject
{
	public ?string $slug;
	public string  $name;
	public ?string $short_name;
	public ?string $description;
	public ?string $color;
	public ?string $icon;
	public bool    $is_active;
	public int     $sort_order;
}