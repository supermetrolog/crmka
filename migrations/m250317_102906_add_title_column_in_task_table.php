<?php

use app\kernel\console\Migration;

/**
 * Class m250317_102906_add_title_column_in_task_table
 */
class m250317_102906_add_title_column_in_task_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = "{{%task}}";

		$this->addColumn($table, 'title', $this->string(255)->notNull());
		$this->alterColumn($table, 'message', $this->text()->defaultValue(null));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = "{{%task}}";

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
	echo "m250317_102906_add_title_column_in_task_table cannot be reverted.\n";

	return false;
	}
	*/
}