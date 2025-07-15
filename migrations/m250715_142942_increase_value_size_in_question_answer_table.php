<?php

use app\kernel\console\Migration;

/**
 * Class m250715_142942_increase_value_size_in_question_answer_table
 */
class m250715_142942_increase_value_size_in_question_answer_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%survey_question_answer}}';
		$this->alterColumn($tableName, 'value', $this->text()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%survey_question_answer}}';
		$this->alterColumn($tableName, 'value', $this->string(4096)->null());
	}
}