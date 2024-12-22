<?php

namespace app\models\search\ChatMember;

use Exception;
use yii\base\BaseObject;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper as YiiArrayHelper;

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

	/**
	 * @throws Exception
	 */
	public function search(array $params): ActiveDataProvider
	{
		$type = YiiArrayHelper::getValue($params, 'model_type');

		$strategy = $this->factory->create($type);

		$strategy->current_user_id        = $this->current_user_id;
		$strategy->current_chat_member_id = $this->current_chat_member_id;

		return $strategy->search($params);
	}
}
