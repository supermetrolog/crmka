<?php

declare(strict_types=1);

namespace app\components\Telegram;

class TelegramMessageAnswerBuilder
{
	private string $text;
	private string $parse_mode           = 'MarkdownV2';
	private array  $entities             = [];
	private bool   $disable_notification = false;
	private array  $reply_markup         = [];

	public static function create(?string $text = null): self
	{
		$builder = new self();

		if ($text) {
			$builder->setText($text);
		}


		return $builder;
	}

	public function setText(string $text): self
	{
		$this->text = $text;

		return $this;
	}

	public function setParseMode(string $parse_mode): self
	{
		$this->parse_mode = $parse_mode;

		return $this;
	}

	public function addEntity(array $config): self
	{
		$this->entities[] = $config;

		return $this;
	}

	public function disableNotification(): self
	{
		$this->disable_notification = true;

		return $this;
	}

	public function addInlineKeyboardButton(array $config): self
	{
		$this->reply_markup['inline_keyboard'][] = $config;

		return $this;
	}

	public function toArray(): array
	{
		return [
			'text'                   => $this->text,
			'parse_mode'             => $this->parse_mode,
			'entities'               => $this->entities,
			'disabled_notifications' => $this->disable_notification,
			'reply_markup'           => $this->reply_markup
		];
	}
}