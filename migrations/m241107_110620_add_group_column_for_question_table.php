<?php

use app\kernel\console\Migration;

/**
 * Class m241107_110620_add_group_column_for_question_table
 */
class m241107_110620_add_group_column_for_question_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'question';
		$this->addColumn($table, 'group', $this->string(64)->null());
		$this->index($table, ['group']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'question';
		$this->dropColumn($table, 'group');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m241107_110620_add_group_column_for_question_table cannot be reverted.\n";

	return false;
	}
	*/
}