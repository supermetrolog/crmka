<?php

namespace app\listeners;

use yii\base\Event;

interface EventListenerInterface
{
	public function handle(Event $event): void;
}