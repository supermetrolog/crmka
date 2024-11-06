<?php

namespace app\services\ChatMemberSystemMessage;

use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use InvalidArgumentException;

abstract class AbstractChatMemberSystemMessage implements ChatMemberSystemMessageInterface
{
	protected string $template = "";

	/** @return static */
	public static function create()
	{
		return new static();
	}

	public function setTemplate(string $template): self
	{
		$this->template = $template;

		return $this;
	}

	abstract public function getTemplateArgs(): array;


	public function validateOrThrow(): void
	{
		$templateArgsCount = StringHelper::substrCount($this->template, '%s');

		if ($templateArgsCount !== ArrayHelper::length($this->getTemplateArgs())) {
			throw new InvalidArgumentException('Template arguments count does not equal real arguments count.');
		}
	}


	public function toMessage(): string
	{
		$this->validateOrThrow();

		return sprintf($this->template, ...$this->getTemplateArgs());
	}
}