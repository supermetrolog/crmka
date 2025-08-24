<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Interfaces;

interface MessageTemplateContextInterface
{
	public function toArray(): array;
}