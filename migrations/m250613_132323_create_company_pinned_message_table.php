<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%company_pinned_message}}`.
 */
class m250613_132323_create_company_pinned_message_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%company_pinned_message}}';

		$this->table($tableName, [
			'id'                     => $this->primaryKey(),
			'company_id'             => $this->integer()->notNull(),
			'chat_member_message_id' => $this->integer()->notNull(),
			'created_by_id'          => $this->integer()->notNull(),
		], $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['company_id']);
		$this->index($tableName, ['chat_member_message_id']);
		$this->index($tableName, ['created_by_id']);

		$this->foreignKey(
			$tableName,
			['company_id'],
			'company',
			['id']
		);

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
		$tableName = '{{%company_pinned_message}}';

		$this->foreignKeyDrop($tableName, ['company_id']);
		$this->foreignKeyDrop($tableName, ['chat_member_message_id']);
		$this->foreignKeyDrop($tableName, ['created_by_id']);

		$this->dropTable($tableName);
	}
}
