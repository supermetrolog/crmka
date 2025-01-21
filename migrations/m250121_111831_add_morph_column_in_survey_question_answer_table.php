<?php

use app\kernel\console\Migration;

/**
 * Class m250121_111831_add_morph_column_in_survey_question_answer_table
 */
class m250121_111831_add_morph_column_in_survey_question_answer_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('survey_question_answer');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('survey_question_answer');
	}
}