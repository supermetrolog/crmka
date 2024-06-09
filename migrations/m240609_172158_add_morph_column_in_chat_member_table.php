<?php

use app\kernel\console\Migration;

/**
 * Class m240609_172158_add_morph_column_in_chat_member_table
 */
class m240609_172158_add_morph_column_in_chat_member_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('chat_member');
		$this->addMorphColumn('call');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('call');
		$this->dropMorphColumn('chat_member');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240609_172158_add_morph_column_in_chat_member_table cannot be reverted.\n";

	return false;
	}
	*/
}