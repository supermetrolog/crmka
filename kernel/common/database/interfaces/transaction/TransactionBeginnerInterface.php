<?php

declare(strict_types=1);

namespace app\kernel\common\database\interfaces\transaction;

interface TransactionBeginnerInterface
{
	public function begin(?string $isolationLevel = null): TransactionInterface;

	/**
	 * @return mixed
	 */
	public function run(callable $callback, ?string $isolationLevel = null);
}