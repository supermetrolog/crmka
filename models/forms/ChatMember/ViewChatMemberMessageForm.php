<?php

declare(strict_types=1);

namespace app\models\forms\ChatMember;

use app\kernel\common\models\Form\Form;
use app\models\ChatMemberMessage;

class ViewChatMemberMessageForm extends Form
{
	public $chat_member_message_id;

	public function rules(): array
	{
		return [
			[['chat_member_message_id'], 'required'],
			[['chat_member_message_id'], 'integer'],
			['chat_member_message_id', 'exist', 'targetClass' => ChatMemberMessage::class, 'targetAttribute' => ['chat_member_message_id' => 'id']],
		];
	}

	public function getChatMemberMessage(): ChatMemberMessage
	{
		return ChatMemberMessage::find()->byId($this->chat_member_message_id)->one();
	}
}