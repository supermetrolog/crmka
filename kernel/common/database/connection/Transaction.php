<?php

declare(strict_types=1);

namespace app\kernel\common\database\connection;

use app\kernel\common\database\interfaces\transaction\TransactionInterface;
use yii\db\Exception;

class Transaction implements TransactionInterface
{
	protected \yii\db\Transaction $transaction;

	public function __construct(\yii\db\Transaction $transaction)
	{
		$this->transaction = $transaction;
	}

	/**
	 * @throws Exception
	 */
	public function commit(): void
	{
		$this->transaction->commit();
	}

	public function rollback(): void
	{
		$this->transaction->rollBack();
	}
}