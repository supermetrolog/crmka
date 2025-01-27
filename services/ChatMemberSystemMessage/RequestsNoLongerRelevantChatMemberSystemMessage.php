<?php

namespace app\services\ChatMemberSystemMessage;

use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use InvalidArgumentException;

class RequestsNoLongerRelevantChatMemberSystemMessage extends AbstractChatMemberSystemMessage
{
	private array $requestIds;

	protected string $template = 'Запросы %s устарели, необходимо архивировать';

	public function validateOrThrow(): void
	{
		parent::validateOrThrow();

		if (ArrayHelper::empty($this->requestIds)) {
			throw new InvalidArgumentException('Request ids must be set');
		}
	}

	public function setRequestIds(array $requestIds): self
	{
		$this->requestIds = $requestIds;

		return $this;
	}

	public function getTemplateArgs(): array
	{
		return [
			StringHelper::join(
				StringHelper::SPACED_COMMA,
				...ArrayHelper::map($this->requestIds, static function ($id) {
				return "#$id";
			})
			)
		];
	}
}