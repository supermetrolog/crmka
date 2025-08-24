<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Resources;

use app\components\MessageTemplate\RenderedMessage;
use app\kernel\web\http\resources\JsonResource;

class MessageTemplateResource extends JsonResource
{
	private string          $template;
	private string          $channel;
	private RenderedMessage $message;

	public function __construct(string $template, string $channel, RenderedMessage $message)
	{
		$this->template = $template;
		$this->channel  = $channel;
		$this->message  = $message;
	}

	public function toArray(): array
	{
		return [
			'template' => $this->template,
			'channel'  => $this->channel,
			'content'  => $this->message->getContent(),
			'mime'     => $this->message->getMimeType(),
			'meta'     => $this->message->getMeta(),
		];
	}
}


