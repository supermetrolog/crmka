<?php

declare(strict_types=1);

namespace app\models\forms\ChatMember;

use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ChatMember;
use app\models\ChatMemberMessage;

class PinChatMemberMessageForm extends Form
{
	public $chat_member_id;
	public $chat_member_message_id;

	public function rules(): array
	{
		return [
			[['chat_member_id', 'chat_member_message_id'], 'required'],
			[['chat_member_id', 'chat_member_message_id'], 'integer'],
			['chat_member_id', 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['chat_member_id' => 'id']],
			[
				'chat_member_message_id',
				'exist',
				'targetClass'     => ChatMemberMessage::class,
				'targetAttribute' => ['chat_member_message_id' => 'id'],
				'filter'          => function (ChatMemberMessageQuery $query) {
					$query->andWhere([
						'OR',
						['=', 'from_chat_member_id', $this->chat_member_id],
						['=', 'to_chat_member_id', $this->chat_member_id],
					]);
				}
			],
		];
	}

	public function getChatMember(): ChatMember
	{
		return ChatMember::find()->byId($this->chat_member_id)->one();
	}

	public function getChatMemberMessage(): ChatMemberMessage
	{
		return ChatMemberMessage::find()->byId($this->chat_member_message_id)->one();
	}
}