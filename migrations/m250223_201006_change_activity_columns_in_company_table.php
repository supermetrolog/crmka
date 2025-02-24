<?php

use app\kernel\console\Migration;

/**
 * Class m250223_201006_change_activity_columns_in_company_table
 */
class m250223_201006_change_activity_columns_in_company_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%company}}';

		$this->alterColumn($tableName, 'activityGroup', $this->integer(11)->null()->defaultValue(null));
		$this->alterColumn($tableName, 'activityProfile', $this->integer(11)->null()->defaultValue(null));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%company}}';

		$this->alterColumn($tableName, 'activityGroup', $this->integer(11)->notNull());
		$this->alterColumn($tableName, 'activityProfile', $this->integer(11)->notNull());
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250223_201006_change_activity_columns_in_company_table cannot be reverted.\n";

	return false;
	}
	*/
}