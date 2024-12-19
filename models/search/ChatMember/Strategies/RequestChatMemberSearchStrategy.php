<?php

namespace app\models\search\ChatMember\Strategies;

use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ChatMember;
use app\models\Company;
use app\models\Request;
use yii\base\ErrorException;

class RequestChatMemberSearchStrategy extends BaseChatMemberSearchStrategy
{
	public $company_id;

	public function rules(): array
	{
		return ArrayHelper::merge(
			parent::rules(),
			[
				[['company_id'], 'integer']
			]
		);
	}

	protected function applySpecificQuery(ChatMemberQuery $query, array $params): void
	{
		$query->joinWith(['request'])
		      ->with([
			      'request.company',
			      'request.regions',
			      'request.directions',
			      'request.districts',
			      'request.objectTypes',
			      'request.objectClasses',
			      'request.consultant.userProfile'
		      ]);
	}

	/**
	 * @throws ErrorException
	 */
	protected function applySpecificFilters(ChatMemberQuery $query, array $params): void
	{
		if (!empty($this->search)) {
			$query->leftJoin(['request_company' => Company::tableName()], ['request_company.id' => Request::xfield('company_id')]);

			$searchWords = StringHelper::explode(StringHelper::SYMBOL_SPACE, $this->search);

			$query->andFilterWhere([
				'or',
				['like', 'request_company.nameEng', $searchWords],
				['like', 'request_company.nameRu', $searchWords]
			]);
		}

		$query->andFilterWhere([Request::field('company_id') => $this->company_id]);
	}

	/**
	 * @throws ErrorException
	 */
	protected function getDefaultSort(): array
	{
		return [
			'asc'  => [
				'cmle.updated_at'                                                 => SORT_ASC,
				'cmm.chat_member_message_id'                                      => SORT_ASC,
				'IF (request.updated_at, request.updated_at, request.created_at)' => SORT_ASC,
				ChatMember::field('id')                                           => SORT_ASC,
			],
			'desc' => [
				'cmle.updated_at'                                                 => SORT_DESC,
				'cmm.chat_member_message_id'                                      => SORT_DESC,
				'IF (request.updated_at, request.updated_at, request.created_at)' => SORT_DESC,
				ChatMember::field('id')                                           => SORT_ASC,
			]
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
					                   ->condition(Request::field('consultant_id') . ' = ' . $this->current_user_id)
					                   ->left('1')
					                   ->right('0')
					                   ->beforeBuild(fn($expression) => "$expression DESC")
					                   ->build(),
					'last_call_rel.created_at'                                        => SORT_ASC,
					'IF (request.updated_at, request.updated_at, request.created_at)' => SORT_ASC
				],
				'desc' => [
					IfExpressionBuilder::create()
					                   ->condition(Request::field('consultant_id') . ' = ' . $this->current_user_id)
					                   ->left('1')
					                   ->right('0')
					                   ->beforeBuild(fn($expression) => "$expression DESC")
					                   ->build(),
					'last_call_rel.created_at'                                        => SORT_DESC,
					'IF (request.updated_at, request.updated_at, request.created_at)' => SORT_DESC
				]
			]
		];
	}
}
