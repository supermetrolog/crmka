<?php

namespace app\services\ChatMemberSystemMessage;

use app\helpers\HTMLHelper;
use app\models\Survey;
use InvalidArgumentException;

class CreateSurveyChatMemberSystemMessage extends AbstractChatMemberSystemMessage
{
	private ?Survey  $survey   = null;
	protected string $template = '%s заполнил(а) опрос %s в результате разговора с %s';

	public function validateOrThrow(): void
	{
		parent::validateOrThrow();

		if (!$this->survey) {
			throw new InvalidArgumentException('Survey must be set');
		}
	}

	public function setSurvey(Survey $survey): self
	{
		$this->survey = $survey;

		return $this;
	}

	public function getTemplateArgs(): array
	{
		return [
			HTMLHelper::bold($this->survey->user->userProfile->getMediumName()),
			HTMLHelper::bold("#{$this->survey->id}"),
			HTMLHelper::bold($this->survey->contact->getFullName())
		];
	}
}