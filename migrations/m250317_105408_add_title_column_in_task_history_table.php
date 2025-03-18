<?php

use app\kernel\console\Migration;

/**
 * Class m250317_105408_add_title_column_in_task_history_table
 */
class m250317_105408_add_title_column_in_task_history_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = "{{%task_history}}";

		$this->addColumn($table, 'title', $this->string(255)->notNull());
		$this->alterColumn($table, 'message', $this->text()->defaultValue(null));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = "{{%task_history}}";

		$this->dropColumn($table, 'title');
		$this->alterColumn($table, 'message', $this->text()->notNull());
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250317_105408_add_title_column_in_task_history_table cannot be reverted.\n";

	return false;
	}
	*/
}