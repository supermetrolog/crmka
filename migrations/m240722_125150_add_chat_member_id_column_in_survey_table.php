<?php

use app\kernel\console\Migration;

/**
 * Class m240722_125150_add_chat_member_id_column_in_survey_table
 */
class m240722_125150_add_chat_member_id_column_in_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'survey';

		$this->addColumn(
			$table,
			'chat_member_id',
			$this->integer()->null()
		);

		$this->index($table, ['chat_member_id']);
		$this->foreignKey(
			$table,
			['chat_member_id'],
			'chat_member',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'survey';

		$this->foreignKeyDrop($table, ['chat_member_id']);
		$this->dropColumn($table, 'chat_member_id');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240722_125150_add_chat_member_id_column_in_survey_table cannot be reverted.\n";

	return false;
	}
	*/
}