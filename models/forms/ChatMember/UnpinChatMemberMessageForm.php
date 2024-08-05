<?php

declare(strict_types=1);

namespace app\models\forms\ChatMember;

use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ChatMember;
use app\models\ChatMemberMessage;

class UnpinChatMemberMessageForm extends Form
{
	public $chat_member_id;

	public function rules(): array
	{
		return [
			[['chat_member_id'], 'required'],
			[['chat_member_id'], 'integer'],
			['chat_member_id', 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['chat_member_id' => 'id']],
		];
	}

	public function getChatMember(): ChatMember
	{
		return ChatMember::find()->byId($this->chat_member_id)->one();
	}
}