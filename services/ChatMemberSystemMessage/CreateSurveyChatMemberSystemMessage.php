<?php

namespace app\services\ChatMemberSystemMessage;

use app\models\Survey;
use InvalidArgumentException;

class CreateSurveyChatMemberSystemMessage implements ChatMemberSystemMessageInterface
{
	private ?Survey $survey   = null;
	private string  $template = '%s заполнил(а) опрос #%s в результате разговора с %s';

	public static function create(): self
	{
		return new self();
	}

	public function validateOrThrow(): void
	{
		if (!$this->survey) {
			throw new InvalidArgumentException('Survey must be set');
		}
	}

	public function setSurvey(Survey $survey): self
	{
		$this->survey = $survey;

		return $this;
	}

	public function toMessage(): string
	{
		$this->validateOrThrow();

		$user    = $this->survey->user;
		$contact = $this->survey->contact;

		return sprintf($this->template, $user->userProfile->getMediumName(), $this->survey->id, $contact->getFullName());
	}
}