<?php

use app\kernel\console\Migration;

/**
 * Class m250207_154503_add_morph_column_in_task_comment_table
 */
class m250210_170646_add_morph_column_in_task_comment_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('task_comment');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('task_comment');
	}
}