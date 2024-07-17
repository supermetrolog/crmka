<?php

declare(strict_types=1);

namespace app\dto\Equipment;

use yii\base\BaseObject;

class CreateEquipmentDto extends BaseObject
{
	public string  $name;
	public string  $address;
	public ?string $description     = null;
	public int     $company_id;
	public int     $contact_id;
	public int     $consultant_id;
	public int     $category;
	public ?int    $availability    = null;
	public ?int    $delivery        = null;
	public ?int    $deliveryPrice   = null;
	public ?int    $price           = null;
	public ?int    $benefit         = null;
	public ?int    $tax             = null;
	public ?int    $count           = null;
	public int     $state;
	public ?int    $status          = null;
	public ?int    $passive_type    = null;
	public ?string $passive_comment = null;
	public string  $created_by_type;
	public int     $created_by_id;
}
