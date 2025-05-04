<?php

namespace app\models\search\ChatMember\Strategies;

use app\helpers\SQLHelper;
use app\models\ActiveQuery\ChatMemberQuery;
use app\models\ChatMember;
use yii\base\ErrorException;
use yii\db\Expression;

class GeneralChatMemberSearchStrategy extends BaseChatMemberSearchStrategy
{
	protected function applySpecificQuery(ChatMemberQuery $query, array $params): void
	{
	}

	/**
	 * @throws ErrorException
	 */
	protected function applySpecificFilters(ChatMemberQuery $query, array $params): void
	{
		// TODO: Search by query

		if ($this->isFilterTrue($this->need_calling)) {
			$interval = new Expression(SQLHelper::dateSub('NOW()', '3 MONTH'));

			$query->andWhere([
				'or',
				['<', 'last_call_rel.created_at', $interval],
				[
					'and',
					['last_call_rel.created_at' => null],
					['<', ChatMember::field('created_at'), $interval]
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
				ChatMember::field('id')      => SORT_ASC,
			],
			'desc' => [
				'cmle.updated_at'            => SORT_DESC,
				'cmm.chat_member_message_id' => SORT_DESC,
				ChatMember::field('id')      => SORT_ASC,
			],
		];
	}

	protected function getSpecificSort(): array
	{
		return [];
	}
}
