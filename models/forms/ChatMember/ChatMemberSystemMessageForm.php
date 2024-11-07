<?php

declare(strict_types=1);

namespace app\models\forms\ChatMember;

use app\dto\ChatMember\CreateChatMemberSystemMessageDto;
use app\kernel\common\models\Form\Form;
use app\models\ActiveQuery\ChatMemberMessageQuery;
use app\models\ChatMember;
use app\models\ChatMemberMessage;
use app\models\ChatMemberMessageTag;
use app\models\Contact;
use app\models\Survey;

class ChatMemberSystemMessageForm extends Form
{
	public $to_chat_member_id;
	public $message     = null;
	public $contact_ids = [];
	public $tag_ids     = [];
	public $reply_to_id = null;
	public $survey_ids  = [];

	public function rules(): array
	{
		return [
			[['to_chat_member_id'], 'required'],
			[['to_chat_member_id', 'reply_to_id'], 'integer'],
			[['message'], 'string', 'max' => 2048],
			[['to_chat_member_id'], 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['to_chat_member_id' => 'id']],
			[['reply_to_id'],
			 'exist',
			 'targetClass'     => ChatMemberMessage::class,
			 'targetAttribute' => ['reply_to_id' => 'id'],
			 'filter'          => function (ChatMemberMessageQuery $query) {
				 $query->andWhere(['to_chat_member_id' => $this->to_chat_member_id]);
			 }],
			['contact_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => Contact::class,
				'targetAttribute' => ['contact_ids' => 'id'],
			]],
			['tag_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => ChatMemberMessageTag::class,
				'targetAttribute' => ['tag_ids' => 'id'],
			]],
			['survey_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => Survey::class,
				'targetAttribute' => ['survey_ids' => 'id'],
			]]
		];
	}

	public function getDto(): CreateChatMemberSystemMessageDto
	{
		return new CreateChatMemberSystemMessageDto([
			'to'         => ChatMember::find()->byId((int)$this->to_chat_member_id)->one(),
			'message'    => $this->message,
			'contactIds' => $this->contact_ids,
			'tagIds'     => $this->tag_ids,
			'replyTo'    => $this->getReplyTo(),
			'surveyIds'  => $this->survey_ids
		]);
	}

	/**
	 * Get reply to message if exists
	 *
	 * @return ChatMemberMessage|null
	 */
	private function getReplyTo(): ?ChatMemberMessage
	{
		if ($this->reply_to_id !== null) {
			return ChatMemberMessage::find()->byId((int)$this->reply_to_id)->one();
		}

		return null;
	}
}