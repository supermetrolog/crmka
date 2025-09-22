<?php
declare(strict_types=1);

namespace app\components\Integrations\Telegram;

class TelegramInlineKeyboardBuilder
{
	protected string  $text;
	protected ?string $url = null;

	public function text(string $text): self
	{
		$this->text = $text;

		return $this;
	}

	public function url(string $url): self
	{
		$this->url = $url;

		return $this;
	}

	public static function link(string $text, string $url): self
	{
		$builder = new self();

		$builder->text($text)->url($url);

		return $builder;
	}

	public function toArray(): array
	{
		return [
			'text' => $this->text,
			'url'  => $this->url
		];
	}
}
