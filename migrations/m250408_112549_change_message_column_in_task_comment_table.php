<?php

use app\kernel\console\Migration;

/**
 * Class m250408_112549_change_message_column_in_task_comment_table
 */
class m250408_112549_change_message_column_in_task_comment_table extends Migration
{
	public function safeUp()
	{
		$table = "{{%task_comment}}";

		$this->alterColumn($table, 'message', $this->string(1024)->notNull());
	}

	public function safeDown()
	{
		$table = "{{%task_comment}}";

		$this->alterColumn($table, 'message', $this->string(255)->notNull());
	}
}