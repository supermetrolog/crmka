<?php

declare(strict_types=1);

namespace app\models\forms\ChatMember;

use app\dto\ChatMember\CreateChatMemberMessageDto;
use app\dto\ChatMember\UpdateChatMemberMessageDto;
use app\kernel\common\models\Form\Form;
use app\models\ChatMember;
use app\models\ChatMemberMessageTag;
use app\models\Contact;

class ChatMemberMessageForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $from_chat_member_id;
	public $to_chat_member_id;
	public $message;
	public $contact_ids = [];
	public $tag_ids     = [];

	public function rules(): array
	{
		return [
			[['message', 'from_chat_member_id', 'to_chat_member_id'], 'required'],
			[['from_chat_member_id', 'to_chat_member_id'], 'integer'],
			[['message'], 'string', 'max' => 2048],
			[['from_chat_member_id'], 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['from_chat_member_id' => 'id']],
			[['to_chat_member_id'], 'exist', 'targetClass' => ChatMember::class, 'targetAttribute' => ['to_chat_member_id' => 'id']],
			['contact_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => Contact::class,
				'targetAttribute' => ['contact_ids' => 'id'],
			]],
			['tag_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => ChatMemberMessageTag::class,
				'targetAttribute' => ['tag_ids' => 'id'],
			]]
		];
	}

	public function scenarios(): array
	{
		$common = [
			'message',
			'contact_ids',
			'tag_ids'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'from_chat_member_id', 'to_chat_member_id'],
			self::SCENARIO_UPDATE => [...$common],
		];
	}

	/**
	 * @return CreateChatMemberMessageDto|UpdateChatMemberMessageDto
	 */
	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateChatMemberMessageDto([
				'from'       => ChatMember::find()->byId((int)$this->from_chat_member_id)->one(),
				'to'         => ChatMember::find()->byId((int)$this->to_chat_member_id)->one(),
				'message'    => $this->message,
				'contactIds' => $this->contact_ids,
				'tagIds'     => $this->tag_ids,
			]);
		}

		return new UpdateChatMemberMessageDto([
			'message'    => $this->message,
			'contactIds' => $this->contact_ids,
			'tagIds'     => $this->tag_ids,
		]);
	}
}