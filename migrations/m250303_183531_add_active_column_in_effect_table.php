<?php

use app\kernel\console\Migration;

/**
 * Class m250303_183531_add_active_column_in_effect_table
 */
class m250303_183531_add_active_column_in_effect_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%effect}}';

		$this->addColumn($tableName, 'active', $this->boolean()->defaultValue(true));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{

		$tableName = '{{%effect}}';

		$this->dropColumn($tableName, 'active');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250303_183531_add_active_column_in_effect_table cannot be reverted.\n";

	return false;
	}
	*/
}