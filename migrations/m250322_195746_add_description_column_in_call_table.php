<?php

use app\kernel\console\Migration;

/**
 * Class m250322_195746_add_description_column_in_call_table
 */
class m250322_195746_add_description_column_in_call_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = "{{%call}}";

		$this->addColumn($table, 'description', $this->string(512)->defaultValue(null));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = "{{%call}}";

		$this->dropColumn($table, 'description');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250322_195746_add_description_column_in_call_table cannot be reverted.\n";

	return false;
	}
	*/
}