<?php

use app\kernel\console\Migration;

/**
 * Class m250601_171247_change_value_column_in_survey_question_answer_table
 */
class m250601_171247_change_value_column_in_survey_question_answer_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%survey_question_answer}}';
		$this->alterColumn($tableName, 'value', $this->string(4096)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%survey_question_answer}}';
		$this->alterColumn($tableName, 'value', $this->string(2048)->null());
	}
}