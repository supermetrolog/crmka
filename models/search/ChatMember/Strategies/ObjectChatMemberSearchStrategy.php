<?php

namespace app\models\search\ChatMember\Strategies;

use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ChatMember;
use app\models\Company;
use app\models\ObjectChatMember;
use app\models\Objects;
use yii\base\ErrorException;

class ObjectChatMemberSearchStrategy extends BaseChatMemberSearchStrategy
{
	public $object_id;
	public $company_id;

	public function rules(): array
	{
		return ArrayHelper::merge(
			parent::rules(),
			[
				[['object_id', 'company_id'], 'integer']
			]
		);
	}

	protected function applySpecificQuery(ChatMemberQuery $query, array $params): void
	{
		$query->joinWith(['objectChatMember.object.consultant chm'])
		      ->with([
			      'objectChatMember.object.company',
			      'objectChatMember.object.offers',
			      'objectChatMember.object.consultant.userProfile'
		      ]);
	}

	/**
	 * @throws ErrorException
	 */
	protected function applySpecificFilters(ChatMemberQuery $query, array $params): void
	{
		if (!empty($this->search)) {
			$query->leftJoin(['object_company' => Company::tableName()], ['object_company.id' => Objects::xfield('company_id')]);

			$searchWords = StringHelper::explode(StringHelper::SYMBOL_SPACE, $this->search);

			$query->andFilterWhere([
				'or',
				['like', 'object_company.nameEng', $searchWords],
				['like', 'object_company.nameRu', $searchWords],
				['like', Objects::field('address'), $searchWords]
			]);
		}

		$query->orFilterWhere([Objects::field('company_id') => $this->company_id]);

		$query->andFilterWhere([
			ObjectChatMember::field('object_id') => $this->object_id,
			'chm.id'                             => $this->consultant_ids
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
				IfExpressionBuilder::create()
				                   ->condition(Objects::field('last_update'))
				                   ->left(Objects::field('last_update'))
				                   ->right(Objects::field('publ_time'))
				                   ->beforeBuild(fn($expression) => "$expression ASC")
				                   ->build(),
				ChatMember::field('id')      => SORT_ASC,
			],
			'desc' => [
				'cmle.updated_at'            => SORT_DESC,
				'cmm.chat_member_message_id' => SORT_DESC,
				IfExpressionBuilder::create()
				                   ->condition(Objects::field('last_update'))
				                   ->left(Objects::field('last_update'))
				                   ->right(Objects::field('publ_time'))
				                   ->beforeBuild(fn($expression) => "$expression DESC")
				                   ->build(),
				ChatMember::field('id')      => SORT_ASC,
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
					'last_call_rel.created_at' => SORT_ASC,
					IfExpressionBuilder::create()
					                   ->condition(Objects::field('last_update'))
					                   ->left(Objects::field('last_update'))
					                   ->right(Objects::field('publ_time'))
					                   ->beforeBuild(fn($expression) => "$expression ASC")
					                   ->build()
				],
				'desc' => [
					'last_call_rel.created_at' => SORT_DESC,
					IfExpressionBuilder::create()
					                   ->condition(Objects::field('last_update'))
					                   ->left(Objects::field('last_update'))
					                   ->right(Objects::field('publ_time'))
					                   ->beforeBuild(fn($expression) => "$expression DESC")
					                   ->build()
				]
			]
		];
	}
}