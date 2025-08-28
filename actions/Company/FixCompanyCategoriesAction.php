<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Category;
use app\models\Company\Company;
use app\models\Objects;
use app\models\Request;
use yii\base\ErrorException;

class FixCompanyCategoriesAction extends Action
{
	public function __construct($id, $controller)
	{

		parent::__construct($id, $controller);
	}

	/**
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function run(): void
	{
		$this->info('Start fix company categories');

		$query = Company::find()
		                ->innerJoinWith(['categories'])
		                ->joinWith(['objects', 'requests'], false)
		                ->andWhere([Request::field('id') => null])
		                ->andWhere(['is not', Objects::field('id'), null])
		                ->andWhere([Category::field('category') => Category::CATEGORY_CLIENT])
		                ->andWhere(['!=', Category::field('category'), Category::CATEGORY_OWNER])
		                ->groupBy(Company::field('id'));

		$this->infof('Found companies: %d', $query->count());

		$changedCompaniesCount = 0;

		/** @var Company $company */
		foreach ($query->each() as $company) {
			Category::deleteAll([Category::field('company_id') => $company->id, Category::field('category') => Category::CATEGORY_CLIENT]);

			$this->createCategory($company, Category::CATEGORY_OWNER);

			$this->comment("Company #{$company->id} categories fixed");

			$changedCompaniesCount++;
		}

		$this->infof('Complete. Edited companies: %d', $changedCompaniesCount);
	}

	/**
	 * @throws SaveModelException
	 */
	private function createCategory(Company $company, int $category): void
	{
		$model = new Category([
			'category'   => $category,
			'company_id' => $company->id
		]);

		$model->saveOrThrow(false);
	}
}