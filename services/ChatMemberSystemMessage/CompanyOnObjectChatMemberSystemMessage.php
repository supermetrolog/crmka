<?php

namespace app\services\ChatMemberSystemMessage;

use app\helpers\HTMLHelper;
use InvalidArgumentException;

class CompanyOnObjectChatMemberSystemMessage extends AbstractChatMemberSystemMessage
{
	private ?int     $surveyId = null;
	private ?int     $objectId = null;
	private ?int     $area     = null;
	protected string $template = '%s  Компания сидит на объекте #%s. Занимает площадь: %s. Подробнее в прикрепленном опросе #%s.';

	public function validateOrThrow(): void
	{
		parent::validateOrThrow();

		if (!$this->surveyId) {
			throw new InvalidArgumentException('Survey id must be set');
		}

		if (!$this->objectId) {
			throw new InvalidArgumentException('Object id must be set');
		}
	}

	public function setSurveyId(int $surveyId): self
	{
		$this->surveyId = $surveyId;

		return $this;
	}

	public function setObjectId(int $objectId): self
	{
		$this->objectId = $objectId;

		return $this;
	}

	public function setArea(?int $area): self
	{
		$this->area = $area;

		return $this;
	}

	public function getTemplateArgs(): array
	{
		if ($this->area) {
			$formattedArea = HTMLHelper::bold(HTMLHelper::squareMeters($this->area));
		} else {
			$formattedArea = 'не указано';
		}

		return [
			HTMLHelper::icon('solid', 'circle-info'),
			HTMLHelper::bold($this->objectId),
			$formattedArea,
			HTMLHelper::bold($this->surveyId),
		];
	}
}