<?php

use app\kernel\console\Migration;

/**
 * Class m250531_154926_add_is_draft_column_in_survey_table
 */
class m250531_154926_add_status_column_in_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'survey';

		$this->addColumns($table, [
			'status'       => $this->string(16)->notNull()->defaultValue('completed'),
			'type'         => $this->string(16)->notNull()->defaultValue('basic'),
			'completed_at' => $this->timestamp()->null()
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'survey';

		$this->dropColumns($table, ['status', 'type', 'completed_at']);
	}
}