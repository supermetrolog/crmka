<?php

namespace app\events;

use yii\base\Event;

abstract class AbstractEvent extends Event implements EventInterface
{
	/** @return static */
	public static function create()
	{
		return new static();
	}
}
