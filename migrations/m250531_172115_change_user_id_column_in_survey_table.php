<?php

use app\kernel\console\Migration;

/**
 * Class m250531_172115_change_user_id_column_in_survey_table
 */
class m250531_172115_change_user_id_column_in_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'survey';

		$this->alterColumn($table, 'user_id', $this->integer()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'survey';

		$this->alterColumn($table, 'user_id', $this->integer()->notNull());
	}
}