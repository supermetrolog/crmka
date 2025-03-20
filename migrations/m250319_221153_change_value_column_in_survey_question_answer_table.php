<?php

use app\kernel\console\Migration;

/**
 * Class m250319_221153_change_value_column_in_survey_question_answer_table
 */
class m250319_221153_change_value_column_in_survey_question_answer_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%survey_question_answer}}';
		$this->alterColumn($tableName, 'value', $this->string(2048)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%survey_question_answer}}';
		$this->alterColumn($tableName, 'value', $this->string(1024)->null());
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250319_221153_change_value_column_in_survey_question_answer_table cannot be reverted.\n";

	return false;
	}
	*/
}