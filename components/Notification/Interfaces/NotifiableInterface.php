<?php

declare(strict_types=1);

namespace app\components\Notification\Interfaces;

interface NotifiableInterface
{
	public function getUserId(): int;
}