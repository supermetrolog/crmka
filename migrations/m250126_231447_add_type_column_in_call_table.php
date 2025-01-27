<?php

use app\kernel\console\Migration;

/**
 * Class m250126_231447_add_type_column_in_call_table
 */
class m250126_231447_add_type_column_in_call_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%call}}';

		$this->addColumn($tableName, 'type', $this->tinyInteger(1)->notNull()->defaultValue(0));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%call}}';

		$this->dropColumn($tableName, 'type');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250126_231447_add_type_column_in_call_table cannot be reverted.\n";

	return false;
	}
	*/
}