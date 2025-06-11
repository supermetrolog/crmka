<?php

namespace app\dto\Company;

use yii\base\BaseObject;

class DisableCompanyDto extends BaseObject
{
	public int     $passive_why;
	public ?string $passive_why_comment;

	public bool $disable_requests = true;
	public bool $disable_contacts = true;
}