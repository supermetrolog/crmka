<?php

namespace app\dto\Company;

use yii\base\BaseObject;

class CompanyMiniModelsDto extends BaseObject
{
	public array $categories = [];

	public array $productRanges = [];
}