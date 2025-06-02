<?php

use app\kernel\console\Migration;

/**
 * Class m250602_212515_add_deleted_at_column_in_survey_table
 */
class m250602_212515_add_deleted_at_column_in_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn('survey', 'deleted_at', $this->timestamp()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropColumn('survey', 'deleted_at');
	}
}