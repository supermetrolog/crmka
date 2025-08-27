<?php

declare(strict_types=1);

namespace app\components\MessageTemplate;

class RenderedMessage
{
	private string $content;
	private string $mimeType;
	private array  $meta;

	public function __construct(string $content, string $mimeType, array $meta = [])
	{
		$this->content  = $content;
		$this->mimeType = $mimeType;
		$this->meta     = $meta;
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function getMimeType(): string
	{
		return $this->mimeType;
	}

	public function getMeta(): array
	{
		return $this->meta;
	}
}


