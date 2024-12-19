<?php

namespace app\models\search\ChatMember;

use yii\base\BaseObject;
use yii\data\ActiveDataProvider;

class ChatMemberSearch extends BaseObject
{
	public $current_chat_member_id;
	public $current_user_id;

	private ChatMemberSearchStrategyFactory $factory;

	public function __construct(ChatMemberSearchStrategyFactory $factory, array $config = [])
	{
		$this->factory = $factory;
		parent::__construct($config);
	}

	public function search(array $params): ActiveDataProvider
	{
		$type = $this->determineType($params);

		$strategy = $this->factory->create($type);

		$strategy->current_user_id        = $this->current_user_id;
		$strategy->current_chat_member_id = $this->current_chat_member_id;

		return $strategy->search($params);
	}

	private function determineType(array $params): ?string
	{
		if (isset($params['model_type'])) {
			return $params['model_type'];
		}

		return null;
	}
}
