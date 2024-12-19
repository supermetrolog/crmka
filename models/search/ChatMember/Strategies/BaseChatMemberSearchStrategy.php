<?php

namespace app\models\search\ChatMember\Strategies;

use app\helpers\ArrayHelper;

abstract class BaseChatMemberSearchStrategy extends AbstractChatMemberSearchStrategy
{
	public $consultant_ids;
	public $need_calling;

	public function rules(): array
	{
		return ArrayHelper::merge(
			parent::rules(),
			[
				[['consultant_ids'], 'each', 'rule' => ['integer']],
				['need_calling', 'boolean']
			]
		);
	}
}
