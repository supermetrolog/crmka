<?php

namespace app\dto\Company;

use yii\base\BaseObject;

class CompanyContactsDto extends BaseObject
{
	public array $emails   = [];
	public array $websites = [];
}