<?php

declare(strict_types=1);

namespace app\kernel\common\database\interfaces\transaction;

interface TransactionInterface
{
	public function commit(): void;
	public function rollback(): void;
}