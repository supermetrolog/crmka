<?php

use app\kernel\console\Migration;

/**
 * Class m241202_101939_add_response_column_in_question_answer_table
 */
class m241202_101939_add_message_column_in_question_answer_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%question_answer}}';

		$this->addColumn($tableName, 'message', $this->string(128)->defaultValue(null));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%question_answer}}';

		$this->dropColumn($tableName, 'message');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m241202_101939_add_response_column_in_question_answer_table cannot be reverted.\n";

	return false;
	}
	*/
}