<?php

use app\kernel\console\Migration;

/**
 * Class m240828_032202_add_impossible_to_column_in_task_table
 */
class m240828_032202_add_impossible_to_column_in_task_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'task';

		$this->addColumn(
			$table,
			'impossible_to',
			$this->timestamp()->null()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'task';
		$this->dropColumn($table, 'impossible_to');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240828_032202_add_impossible_to_column_in_task_table cannot be reverted.\n";

	return false;
	}
	*/
}