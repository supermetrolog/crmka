<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class EventManager implements BootstrapInterface
{
	public array $config;

	/**
	 * @throws NotInstantiableException
	 * @throws InvalidConfigException
	 */
	public function bootstrap($app): void
	{
		foreach ($this->config as $event => $listeners) {
			foreach ($listeners as $listener) {
				$listener = Yii::$container->get($listener);
				Event::on(self::class, $event, [$listener, 'handle']);
			}
		}
	}

	public function trigger(Event $event): void
	{
		Event::trigger(self::class, get_class($event), $event);
	}
}