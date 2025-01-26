<?php

use app\kernel\console\Migration;

/**
 * Class m250126_230032_add_status_column_in_call_table
 */
class m250126_230032_add_status_column_in_call_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%call}}';

		$this->addColumn($tableName, 'status', $this->tinyInteger(1)->notNull()->defaultValue(1));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%call}}';

		$this->dropColumn($tableName, 'status');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250126_230032_add_status_column_in_call_table cannot be reverted.\n";

	return false;
	}
	*/
}