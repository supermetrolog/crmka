<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\CompanyMiniModelsDto;
use app\helpers\NumberHelper;
use app\helpers\StringHelper;
use app\kernel\common\models\Form\Form;

class CompanyMiniModelsForm extends Form
{
	/** @var array<object{product: string}> */
	public $productRanges;

	/** @var array<object{category: integer}> */
	public $categories;

	public function rules(): array
	{
		return [
			['productRanges', 'validateProductRanges', 'skipOnEmpty' => true],
			['categories', 'validateCategories', 'skipOnEmpty' => true],
		];
	}

	public function validateProductRanges($attribute): void
	{
		foreach ($this->productRanges as $productRange) {
			if (!isset($productRange['product']) || !StringHelper::isString($productRange['product'])) {
				$this->addError($attribute, 'Некорректно указано название товара');
			}
		}
	}

	public function validateCategories($attribute): void
	{
		foreach ($this->categories as $category) {
			if (!isset($category['category']) || !NumberHelper::isNumber($category['category'])) {
				$this->addError($attribute, 'Некорректно указана категория');
			}
		}
	}

	public function getDto(): CompanyMiniModelsDto
	{
		return new CompanyMiniModelsDto([
			'productRanges' => $this->productRanges,
			'categories'    => $this->categories
		]);
	}
}