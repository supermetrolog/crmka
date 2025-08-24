<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Interfaces;

interface MessageTemplateFormInterface
{
	public function rules(): array;

	public function getDto(): MessageTemplateDtoInterface;

	public function validateOrThrow(): void;
}