<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Factories;

use app\components\MessageTemplate\Enums\MessageTemplateEnum;
use app\components\MessageTemplate\Interfaces\MessageTemplateInterface;
use app\components\MessageTemplate\Templates\ResumeCompanyCooperation\ResumeCompanyCooperationMessageTemplate;
use InvalidArgumentException;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class TemplateFactory
{
	/** @var array<string, class-string<MessageTemplateInterface>> */
	private array $templates = [
		MessageTemplateEnum::RESUME_COMPANY_COOPERATION => ResumeCompanyCooperationMessageTemplate::class
	];

	public function has(string $key): bool
	{
		return isset($this->templates[$key]);
	}

	/**
	 * @throws InvalidConfigException
	 * @throws NotInstantiableException
	 */
	public function create(string $key): MessageTemplateInterface
	{
		if (!$this->has($key)) {
			throw new InvalidArgumentException('Unknown template: ' . $key);
		}

		/** @var MessageTemplateInterface */
		return Yii::$container->get($this->templates[$key]);
	}
}


