<?php

declare(strict_types=1);

namespace app\kernel\common\database\connection;

use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\database\interfaces\transaction\TransactionInterface;
use Exception;
use LogicException;
use Throwable;

class Connection extends \yii\db\Connection implements TransactionBeginnerInterface
{
	public function begin(?string $isolationLevel = null): TransactionInterface
	{
		$tx = $this->beginTransaction();

		if ($tx === null) {
			throw new LogicException('Transaction begin error');
		}

		return new Transaction($tx);
	}

	/**
	 * @return mixed
	 * @throws Throwable
	 * @throws Exception
	 */
	public function run(callable $callback, ?string $isolationLevel = null)
	{
		return $this->transaction($callback, $isolationLevel);
	}
}