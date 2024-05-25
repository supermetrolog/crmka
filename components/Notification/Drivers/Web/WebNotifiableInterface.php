<?php

declare(strict_types=1);

namespace app\components\Notification\Drivers\Web;

interface WebNotifiableInterface
{
	public function getId(): int;
}