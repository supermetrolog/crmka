<?php

namespace app\dto\Company;

use yii\base\BaseObject;

class CompanyMiniModelsDto extends BaseObject
{
	/** @var array<object{category: int}> */
	public array $categories = [];

	/** @var array<object{product: string}> */
	public array $productRanges = [];
}