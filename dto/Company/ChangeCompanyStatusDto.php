<?php

namespace app\dto\Company;

use app\enum\Company\CompanyStatusSourceEnum;
use app\models\User\User;
use yii\base\BaseObject;

class ChangeCompanyStatusDto extends BaseObject
{
	public int     $status;
	public ?string $reason    = null;
	public ?string $comment   = null;
	public ?User   $initiator = null;
	public string  $source    = CompanyStatusSourceEnum::USER;
}