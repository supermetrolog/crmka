<?php

namespace app\services\ChatMemberSystemMessage;

use app\helpers\HTMLHelper;
use InvalidArgumentException;

class CompanyPlannedDevelopChatMemberSystemMessage extends AbstractChatMemberSystemMessage
{
	private ?int     $surveyId = null;
	protected string $template = '%s  Компания планирует развитие. Подробнее в прикрепленном опросе #%s.';

	public function validateOrThrow(): void
	{
		parent::validateOrThrow();

		if (!$this->surveyId) {
			throw new InvalidArgumentException('Survey id must be set');
		}
	}

	public function setSurveyId(int $surveyId): self
	{
		$this->surveyId = $surveyId;

		return $this;
	}

	public function getTemplateArgs(): array
	{
		return [
			HTMLHelper::icon('solid', 'rocket'),
			HTMLHelper::bold($this->surveyId),
		];
	}
}