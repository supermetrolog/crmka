<?php

declare(strict_types=1);

namespace app\dto\Equipment;

use yii\base\BaseObject;

class CreateEquipmentDto extends BaseObject
{
	public ?string $name;
	public string  $address;
	public ?string $description;
	public int     $company_id;
	public int     $contact_id;
	public int     $consultant_id;
	public int     $category;
	public int     $availability;
	public int     $delivery;
	public ?int    $deliveryPrice   = null;
	public int     $price;
	public int     $benefit;
	public int     $tax;
	public int     $count;
	public int     $state;
	public int     $status;
	public ?int    $passive_type    = null;
	public ?string $passive_comment = null;
	public string  $created_by_type;
	public int     $created_by_id;
}
