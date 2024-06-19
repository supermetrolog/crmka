<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%chat_member_last_event}}`.
 */
class m240617_153917_create_chat_member_last_event_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%chat_member_last_event}}';
		$this->table($tableName, [
			'id'                   => $this->primaryKey(),
			'chat_member_id'       => $this->integer()->notNull(),
			'event_chat_member_id' => $this->integer()->notNull(),
		], $this->timestamps());

		$this->index($tableName, ['chat_member_id']);
		$this->index($tableName, ['event_chat_member_id']);

		$this->foreignKey($tableName, ['chat_member_id'], 'chat_member', ['id']);
		$this->foreignKey($tableName, ['event_chat_member_id'], 'chat_member', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%chat_member_last_event}}';

		$this->foreignKeyDrop($tableName, ['event_chat_member_id']);
		$this->foreignKeyDrop($tableName, ['chat_member_id']);
		$this->dropTable($tableName);
	}
}
