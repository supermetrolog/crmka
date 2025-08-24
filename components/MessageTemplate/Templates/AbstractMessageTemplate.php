<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Templates;

use app\components\MessageTemplate\Interfaces\ChannelTemplateInterface;
use app\components\MessageTemplate\Interfaces\MessageTemplateInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

abstract class AbstractMessageTemplate implements MessageTemplateInterface
{
	protected array  $templatesByChannel = [];
	protected string $key;

	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 */
	public function getChannelTemplate(string $channelId): ChannelTemplateInterface
	{
		/** @var ChannelTemplateInterface */
		return Yii::$container->get($this->templatesByChannel[$channelId]);
	}
}