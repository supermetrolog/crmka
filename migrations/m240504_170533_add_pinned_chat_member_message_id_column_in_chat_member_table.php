<?php

use app\kernel\console\Migration;

/**
 * Class m240504_170533_add_pinned_chat_member_message_id_column_in_chat_member_table
 */
class m240504_170533_add_pinned_chat_member_message_id_column_in_chat_member_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'chat_member';
		$this->addColumn(
			$table,
			'pinned_chat_member_message_id',
			$this->integer()->null()
		);

		$this->index($table, ['pinned_chat_member_message_id']);
		$this->foreignKey(
			$table,
			['pinned_chat_member_message_id'],
			'chat_member_message',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'chat_member';
		$this->foreignKeyDrop($table, ['pinned_chat_member_message_id']);
		$this->dropColumn($table, 'pinned_chat_member_message_id');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240504_170533_add_pinned_chat_member_message_id_column_in_chat_member_table cannot be reverted.\n";

	return false;
	}
	*/
}