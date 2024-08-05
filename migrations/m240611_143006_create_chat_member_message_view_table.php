<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%chat_member_message_view}}`.
 */
class m240611_143006_create_chat_member_message_view_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%chat_member_message_view}}';
		$this->table($tableName, [
			'id'                     => $this->primaryKey(),
			'chat_member_id'         => $this->integer()->notNull(),
			'chat_member_message_id' => $this->integer()->notNull(),
		], $this->timestamps());

		$this->index($tableName, ['chat_member_id']);
		$this->index($tableName, ['chat_member_message_id']);

		$this->foreignKey($tableName, ['chat_member_id'], 'chat_member', ['id']);
		$this->foreignKey($tableName, ['chat_member_message_id'], 'chat_member_message', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%chat_member_message_view}}';

		$this->foreignKeyDrop($tableName, ['chat_member_message_id']);
		$this->foreignKeyDrop($tableName, ['chat_member_id']);
		$this->dropTable($tableName);
	}
}
