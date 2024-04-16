<?php

use app\kernel\console\Migration;

/**
 * Class m240416_164736_add_deleted_at_column_in_chat_member_message_table
 */
class m240416_164736_add_deleted_at_column_in_chat_member_message_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn('chat_member_message', 'deleted_at', $this->timestamp()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropColumn('chat_member_message', 'deleted_at');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240416_164736_add_deleted_at_column_in_chat_member_message_table cannot be reverted.\n";

	return false;
	}
	*/
}