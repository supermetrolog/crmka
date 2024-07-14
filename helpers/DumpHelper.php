<?php

declare(strict_types=1);

namespace app\helpers;

class DumpHelper
{

	public static function setDumpToServerFormat(): void
	{
		$_SERVER['VAR_DUMPER_FORMAT'] = 'tcp://127.0.0.1:9912';
	}

	public static function dds($value): void
	{
		self::setDumpToServerFormat();
		dd($value);
	}
}