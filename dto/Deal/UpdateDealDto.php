<?php

declare(strict_types=1);

namespace app\dto\Deal;

use app\models\Company\Company;
use app\models\Complex;
use app\models\Objects;
use app\models\Request;
use app\models\User\User;
use DateTimeInterface;
use yii\base\BaseObject;

class UpdateDealDto extends BaseObject
{
	public ?string            $name;
	public Objects            $object;
	public int                $is_our;
	public int                $is_competitor;
	public int                $type_id;
	public ?Company           $competitor         = null;
	public ?Company           $company            = null;
	public ?Complex           $complex            = null;
	public ?int               $original_id;
	public ?string            $clientLegalEntity  = null;
	public ?int               $formOfOrganization = null;
	public Request            $request;
	public ?int               $area;
	public ?int               $floorPrice;
	public User               $consultant;
	public ?DateTimeInterface $dealDate;
	public ?int               $contractTerm;
	public ?string            $description        = null;
	public ?string            $visual_id;
}
