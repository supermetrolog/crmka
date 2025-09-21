<?php

namespace app\dto\ContactPosition;

use app\models\User\User;
use yii\base\BaseObject;

class CreateContactPositionDto extends BaseObject
{
	public ?User   $createdBy;
	public ?string $slug;
	public string  $name;
	public ?string $short_name;
	public ?string $description;
	public ?string $color;
	public ?string $icon;
	public bool    $is_active;
	public int     $sort_order;
}