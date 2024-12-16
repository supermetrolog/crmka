<?php

namespace app\models\search\ChatMember\Strategies;

use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\helpers\StringHelper;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ChatMember;
use app\models\Company;
use yii\base\ErrorException;

class CompanyChatMemberSearchStrategy extends BaseChatMemberSearchStrategy
{
	protected function applySpecificQuery(ChatMemberQuery $query, array $params): void
	{
		$query->joinWith(['company'])
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
			$searchWords = StringHelper::explode(StringHelper::SYMBOL_SPACE, $this->search);

			$query->andFilterWhere([
				'or',
				['like', Company::field('nameEng'), $searchWords],
				['like', Company::field('nameRu'), $searchWords],
			]);
		}

		$query->andFilterWhere([
			Company::field('consultant_id') => $this->consultant_ids
		]);
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
					                   ->condition(Company::field('consultant_id') . ' = ' . $this->current_user_id)
					                   ->left('1')
					                   ->right('0')
					                   ->beforeBuild(fn($expression) => "$expression DESC")
					                   ->build(),
					'last_call_rel.created_at'   => SORT_ASC,
					Company::field('updated_at') => SORT_ASC
				],
				'desc' => [
					IfExpressionBuilder::create()
					                   ->condition(Company::field('consultant_id') . ' = ' . $this->current_user_id)
					                   ->left('1')
					                   ->right('0')
					                   ->beforeBuild(fn($expression) => "$expression DESC")
					                   ->build(),
					'last_call_rel.created_at'   => SORT_DESC,
					Company::field('updated_at') => SORT_DESC
				]
			]
		];
	}
}
