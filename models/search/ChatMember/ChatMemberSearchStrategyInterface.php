<?php

namespace app\models\search\ChatMember;

use yii\data\ActiveDataProvider;

interface ChatMemberSearchStrategyInterface
{

	public function search(array $params): ActiveDataProvider;
}
