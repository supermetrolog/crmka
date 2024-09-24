<?php

use app\kernel\console\Migration;

/**
 * Class m240924_113352_add_reply_to_id_column_in_chat_member_message_table
 */
class m240924_113352_add_reply_to_id_column_in_chat_member_message_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'chat_member_message';

		$this->addColumn(
			$table,
			'reply_to_id',
			$this->integer()->null()
		);

		$this->index($table, ['reply_to_id']);
		$this->foreignKey(
			$table,
			['reply_to_id'],
			'chat_member_message',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'chat_member_message';
		$this->foreignKeyDrop($table, ['reply_to_id']);
		$this->dropColumn($table, 'reply_to_id');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240924_113352_add_reply_to_id_column_in_chat_member_message_table cannot be reverted.\n";

	return false;
	}
	*/
}