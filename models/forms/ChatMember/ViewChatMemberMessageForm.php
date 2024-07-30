<?php

declare(strict_types=1);

namespace app\models\forms\ChatMember;

use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\ChatMemberMessage;

class ViewChatMemberMessageForm extends Form
{
	public $chat_member_message_id;
	public $from_chat_member_id;

	public function rules(): array
	{
		return [
			[['chat_member_message_id', 'from_chat_member_id'], 'required'],
			[['chat_member_message_id', 'from_chat_member_id'], 'integer'],
			['chat_member_message_id', 'exist', 'targetClass' => ChatMemberMessage::class, 'targetAttribute' => ['chat_member_message_id' => 'id']],
			['from_chat_member_id', 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['from_chat_member_id' => 'id']],
		];
	}

	public function getChatMemberMessage(): ChatMemberMessage
	{
		return ChatMemberMessage::find()->byId($this->chat_member_message_id)->one();
	}
}