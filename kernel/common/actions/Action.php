<?php

declare(strict_types=1);

namespace app\kernel\common\actions;

use yii\helpers\BaseConsole;

abstract class Action extends \yii\base\Action
{
	abstract public function run(): void;

	public function info(string $message): void
	{
		$this->controller->stdout($message . PHP_EOL, BaseConsole::FG_CYAN);
	}

	public function infof(string $message, ...$values): void
	{
		$this->controller->stdout(sprintf($message, ...$values) . PHP_EOL, BaseConsole::FG_CYAN);
	}

	public function error(string $message): void
	{
		$this->controller->stdout($message . PHP_EOL, BaseConsole::FG_RED);
	}

	public function warning(string $message): void
	{
		$this->controller->stdout($message . PHP_EOL, BaseConsole::FG_YELLOW);
	}

	public function comment(string $message): void
	{
		$this->controller->stdout($message . PHP_EOL, BaseConsole::FG_GREEN);
	}

	public function confirm(string $message, $default = false): void
	{
		$this->controller->confirm($message, $default);
	}
}