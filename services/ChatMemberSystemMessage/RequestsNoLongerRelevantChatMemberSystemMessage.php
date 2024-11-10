<?php

namespace app\services\ChatMemberSystemMessage;

class RequestsNoLongerRelevantChatMemberSystemMessage extends AbstractChatMemberSystemMessage
{
	protected string $template = 'Текущие запросы компании устарели. Отправьте их в архив и создайте новые, если это необходимо.';

	public function getTemplateArgs(): array
	{
		return [];
	}
}