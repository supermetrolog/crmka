<?php

declare(strict_types=1);

namespace app\kernel\common\actions;

use yii\helpers\BaseConsole;

class Action extends \yii\base\Action
{
	public function print(string $message, int $color = BaseConsole::FG_CYAN): void
	{
		$this->controller->stdout($message . PHP_EOL, $color);
	}

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

	public function commentf(string $message, ...$values): void
	{
		$this->controller->stdout(sprintf($message, ...$values) . PHP_EOL, BaseConsole::FG_GREEN);
	}

	public function confirm(string $message, $default = false): bool
	{
		return $this->controller->confirm($message, $default);
	}

	public function delimiter(string $symbol = '=', int $repeat = 50): void
	{
		$this->controller->stdout(str_repeat($symbol, $repeat) . PHP_EOL);
	}
}