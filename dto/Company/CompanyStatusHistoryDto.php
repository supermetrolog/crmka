<?php

namespace app\dto\Company;

use app\models\Company\Company;
use app\models\User;
use yii\base\BaseObject;

class CompanyStatusHistoryDto extends BaseObject
{
	public Company $company;
	public ?User   $changedBy = null;
	public string  $changedBySource;

	public int     $status;
	public ?string $reason;
	public ?string $comment;
}