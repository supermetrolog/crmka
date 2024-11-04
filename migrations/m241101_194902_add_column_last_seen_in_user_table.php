<?php

use app\kernel\console\Migration;

/**
 * Class m241101_194902_add_column_last_seen_in_user_table
 */
class m241101_194902_add_column_last_seen_in_user_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = '{{%user}}';

		$this->addColumn($table, 'last_seen', $this->timestamp());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = '{{%user}}';

		$this->dropColumn($table, 'last_seen');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m241101_194902_add_column_last_seen_in_user_table cannot be reverted.\n";

	return false;
	}
	*/
}