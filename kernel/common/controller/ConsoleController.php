<?php

namespace app\kernel\common\controller;

use yii\console\Controller;
use yii\helpers\BaseConsole;

class ConsoleController extends Controller
{
	public function print(string $message, ...$params): void
	{
		$this->stdout($message . PHP_EOL, ...$params);
	}

	public function info(string $message): void
	{
		$this->print($message, BaseConsole::FG_CYAN);
	}

	public function infof(string $message, ...$values): void
	{
		$this->info(sprintf($message, ...$values));
	}

	public function error(string $message): void
	{
		$this->print($message, BaseConsole::FG_RED);
	}

	public function warning(string $message): void
	{
		$this->print($message, BaseConsole::FG_YELLOW);
	}

	public function comment(string $message): void
	{
		$this->print($message, BaseConsole::FG_BLUE);
	}

	public function commentf(string $message, ...$values): void
	{
		$this->comment(sprintf($message, ...$values));
	}

	public function success(string $message): void
	{
		$this->print($message, BaseConsole::FG_GREEN);
	}
}