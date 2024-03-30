<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%chat_member_message_task}}`.
 */
class m240330_115544_create_chat_member_message_task_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = '{{%chat_member_message_task}}';

		$this->table($table, [
			'id'                     => $this->primaryKey(),
			'task_id'                => $this->integer()->notNull(),
			'chat_member_message_id' => $this->integer()->notNull()
		], $this->timestamps());

		$this->index($table, ['task_id']);
		$this->index($table, ['chat_member_message_id']);

		$this->foreignKey(
			$table,
			['task_id'],
			'task',
			['id']
		);

		$this->foreignKey(
			$table,
			['chat_member_message_id'],
			'chat_member_message',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = '{{%chat_member_message_task}}';

		$this->foreignKeyDrop($table, ['chat_member_message_id']);
		$this->foreignKeyDrop($table, ['task_id']);
		$this->dropTable($table);
	}
}
