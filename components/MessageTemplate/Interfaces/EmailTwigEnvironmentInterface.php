<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Interfaces;

interface EmailTwigEnvironmentInterface
{
	public function render($name, array $context = []): string;
}