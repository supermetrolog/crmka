<?php

declare(strict_types=1);

namespace app\models\forms\ChatMember;

use app\dto\ChatMemberView\StatisticChatMemberViewDto;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;

class StatisticChatMemberViewForm extends Form
{
	public $model_types;
	public $chat_member_ids;

	public function rules(): array
	{
		return [
			[['model_types', 'chat_member_ids'], 'required'],
			['chat_member_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => ChatMember::class,
				'targetAttribute' => ['chat_member_ids' => 'id'],
			]],
			['model_types', 'each', 'rule' => [
				'string'
			]]
		];
	}

	public function getDto(): StatisticChatMemberViewDto
	{
		return new StatisticChatMemberViewDto([
			'chat_member_ids' => $this->chat_member_ids,
			'model_types'     => $this->model_types
		]);
	}
}