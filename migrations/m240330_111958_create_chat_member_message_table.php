<?php

use app\kernel\console\Migration;


/**
 * Handles the creation of table `{{%chat_member_message}}`.
 */
class m240330_111958_create_chat_member_message_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%chat_member_message}}';

		$this->table($tableName, [
			'id'                  => $this->primaryKey(),
			'to_chat_member_id'   => $this->integer()->notNull(),
			'from_chat_member_id' => $this->integer(),
			'message'             => $this->text(),
		], $this->timestamps());

		$this->index($tableName, ['to_chat_member_id']);
		$this->index($tableName, ['from_chat_member_id']);

		$refTable = '{{%chat_member}}';

		$this->foreignKey(
			$tableName,
			['to_chat_member_id'],
			$refTable,
			['id']
		);

		$this->foreignKey(
			$tableName,
			['from_chat_member_id'],
			$refTable,
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%chat_member_message}}';

		$this->foreignKeyDrop($tableName, ['from_chat_member_id']);
		$this->foreignKeyDrop($tableName, ['to_chat_member_id']);

		$this->dropTable($tableName);
	}
}
