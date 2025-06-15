<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\PinMessageCompanyDto;
use app\kernel\common\models\Form\Form;
use app\models\ChatMemberMessage;
use app\models\User;

class CompanyPinMessageForm extends Form
{
	public User   $user;
	public string $message_id;

	public function rules(): array
	{
		return [
			[['message_id', 'user'], 'required'],
			[['message_id'], 'integer'],
			[['message_id'], 'exist', 'targetClass' => ChatMemberMessage::class, 'targetAttribute' => ['message_id' => 'id']],
		];
	}

	public function getDto(): PinMessageCompanyDto
	{
		return new PinMessageCompanyDto([
			'message' => ChatMemberMessage::find()->byId((int)$this->message_id)->one(),
			'user'    => $this->user,
		]);
	}
} 