<?php

declare(strict_types=1);

namespace app\models\forms\Company;

use app\dto\Company\LinkMessageCompanyDto;
use app\enum\EntityMessageLink\EntityMessageLinkKindEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\Form\Form;
use app\models\ChatMemberMessage;
use app\models\User\User;

class CompanyLinkMessageForm extends Form
{
	public User   $user;
	public string $message_id;
	public string $kind;

	public function rules(): array
	{
		return [
			[['message_id', 'user', 'kind'], 'required'],
			[['message_id'], 'integer'],
			['kind', 'string'],
			['kind', EnumValidator::class, 'enumClass' => EntityMessageLinkKindEnum::class],
			[['message_id'], 'exist', 'targetClass' => ChatMemberMessage::class, 'targetAttribute' => ['message_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'message_id' => 'ID сообщения',
			'user'       => 'Пользователь',
			'kind'       => 'Тип привязки'
		];
	}

	public function getDto(): LinkMessageCompanyDto
	{
		return new LinkMessageCompanyDto([
			'message' => ChatMemberMessage::find()->byId((int)$this->message_id)->one(),
			'user'    => $this->user,
			'kind'    => $this->kind
		]);
	}
} 