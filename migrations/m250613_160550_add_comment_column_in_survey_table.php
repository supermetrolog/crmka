<?php

use app\kernel\console\Migration;

/**
 * Class m250613_160550_add_comment_column_in_survey_table
 */
class m250613_160550_add_comment_column_in_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn('survey', 'comment', $this->text()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropColumn('survey', 'comment');
	}
}