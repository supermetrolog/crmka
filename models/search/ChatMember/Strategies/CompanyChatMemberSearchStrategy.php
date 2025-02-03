<?php

namespace app\models\search\ChatMember\Strategies;

use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\helpers\ArrayHelper;
use app\helpers\SQLHelper;
use app\helpers\StringHelper;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\Category;
use app\models\ChatMember;
use app\models\Company;
use yii\base\ErrorException;
use yii\db\Expression;

class CompanyChatMemberSearchStrategy extends BaseChatMemberSearchStrategy
{
	public $categories;

	public function rules(): array
	{
		return ArrayHelper::merge(
			parent::rules(),
			[
				[['categories'], 'each', 'rule' => ['integer']]
			]
		);
	}

	protected function applySpecificQuery(ChatMemberQuery $query, array $params): void
	{
		$query->joinWith(['company.categories'])
		      ->with([
			      'company.logo',
			      'company.categories',
			      'company.companyGroup',
			      'company.consultant'
		      ]);
	}

	/**
	 * @throws ErrorException
	 */
	protected function applySpecificFilters(ChatMemberQuery $query, array $params): void
	{
		if (!empty($this->search)) {
			$searchWords = StringHelper::toWords($this->search);

			$query->andFilterWhere([
				'or',
				['like', Company::field('nameEng'), $searchWords],
				['like', Company::field('nameRu'), $searchWords],
			]);
		}

		$query->andFilterWhere([
			Company::field('consultant_id') => $this->consultant_ids,
			Category::field('category')     => $this->categories,
		]);

		if ($this->isFilterTrue($this->need_calling)) {
			$interval = new Expression(SQLHelper::dateSub('NOW()', '3 MONTH'));

			$query->andWhere([
				'or',
				['<', 'last_call_rel.created_at', $interval],
				[
					'and',
					['last_call_rel.created_at' => null],
					['<', Company::field('updated_at'), $interval]
				],
				[]
			]);
		}
	}

	/**
	 * @throws ErrorException
	 */
	protected function getDefaultSort(): array
	{
		return [
			'asc'  => [
				'cmle.updated_at'            => SORT_ASC,
				'cmm.chat_member_message_id' => SORT_ASC,
				Company::field('updated_at') => SORT_ASC,
				ChatMember::field('id')      => SORT_ASC,
			],
			'desc' => [
				'cmle.updated_at'            => SORT_DESC,
				'cmm.chat_member_message_id' => SORT_DESC,
				Company::field('updated_at') => SORT_DESC,
				ChatMember::field('id')      => SORT_ASC,
			],
		];
	}

	/**
	 * @throws ErrorException
	 */
	protected function getSpecificSort(): array
	{
		return [
			'call' => [
				'asc'  => [
					IfExpressionBuilder::create()
					                   ->condition(Company::field('consultant_id') . ' = :user_id', [':user_id' => $this->current_user_id])
					                   ->left('1')
					                   ->right('0')
					                   ->beforeBuild(fn($expression) => "$expression DESC")
					                   ->build(),
					'COALESCE(last_call_rel.created_at, company.updated_at, company.created_at)' => SORT_ASC
				],
				'desc' => [
					IfExpressionBuilder::create()
					                   ->condition(Company::field('consultant_id') . ' = :user_id', [':user_id' => $this->current_user_id])
					                   ->left('1')
					                   ->right('0')
					                   ->beforeBuild(fn($expression) => "$expression DESC")
					                   ->build(),
					'COALESCE(last_call_rel.created_at, company.updated_at, company.created_at)' => SORT_DESC
				]
			]
		];
	}
}
