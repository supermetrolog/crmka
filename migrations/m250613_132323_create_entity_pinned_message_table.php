<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%company_pinned_message}}`.
 */
class m250613_132323_create_entity_pinned_message_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%entity_pinned_message}}';

		$this->table($tableName, [
			'id'                     => $this->primaryKey(),
			'chat_member_message_id' => $this->integer()->notNull(),
			'created_by_id'          => $this->integer()->notNull(),
		], $this->timestamps(), $this->softDelete(), $this->morph('entity'));

		$this->index($tableName, ['entity_id', 'entity_type']);
		$this->index($tableName, ['chat_member_message_id']);
		$this->index($tableName, ['created_by_id']);

		$this->foreignKey(
			$tableName,
			['chat_member_message_id'],
			'chat_member_message',
			['id']
		);

		$this->foreignKey(
			$tableName,
			['created_by_id'],
			'user',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%entity_pinned_message}}';

		$this->foreignKeyDrop($tableName, ['chat_member_message_id']);
		$this->foreignKeyDrop($tableName, ['created_by_id']);

		$this->dropTable($tableName);
	}
}
