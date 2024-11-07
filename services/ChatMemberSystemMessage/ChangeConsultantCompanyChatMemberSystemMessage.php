<?php

namespace app\services\ChatMemberSystemMessage;

use app\helpers\HTMLHelper;
use app\models\User;
use InvalidArgumentException;

class ChangeConsultantCompanyChatMemberSystemMessage extends AbstractChatMemberSystemMessage
{
	protected string $template = 'Ответственный консультант компании изменен с %s на %s';

	private ?User $oldConsultant = null;
	private ?User $consultant    = null;

	public function validateOrThrow(): void
	{
		parent::validateOrThrow();

		if (!$this->oldConsultant) {
			throw new InvalidArgumentException('Old consultant must be set');
		}

		if (!$this->consultant) {
			throw new InvalidArgumentException('New consultant must be set');
		}
	}

	public function setConsultant(User $consultant): self
	{
		$this->consultant = $consultant;

		return $this;
	}

	public function setOldConsultant(User $oldConsultant): self
	{
		$this->oldConsultant = $oldConsultant;

		return $this;
	}

	public function getTemplateArgs(): array
	{
		return [
			HTMLHelper::deleted($this->oldConsultant->userProfile->getMediumName()),
			HTMLHelper::bold($this->consultant->userProfile->getMediumName()),
		];
	}
}