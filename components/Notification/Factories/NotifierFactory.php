<?php

declare(strict_types=1);

namespace app\components\Notification\Factories;

use app\components\Notification\Notifier;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class NotifierFactory
{
	/**
	 * @throws NotInstantiableException
	 * @throws InvalidConfigException
	 */
	public function create(): Notifier
	{
		return Yii::$container->get(Notifier::class);
	}
}