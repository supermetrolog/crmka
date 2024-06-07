<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%survey_question_answer}}`.
 */
class m240607_211040_create_survey_question_answer_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%survey_question_answer}}';
		$this->table($tableName, [
			'id'                 => $this->primaryKey(),
			'survey_id'          => $this->integer()->notNull(),
			'question_answer_id' => $this->integer()->notNull(),
			'value'              => $this->string()->null(),
		]);

		$this->index($tableName, ['survey_id']);
		$this->index($tableName, ['question_answer_id']);

		$this->foreignKey($tableName, ['survey_id'], 'survey', ['id']);
		$this->foreignKey($tableName, ['question_answer_id'], 'question_answer', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%survey_question_answer}}';

		$this->foreignKeyDrop($tableName, ['survey_id']);
		$this->foreignKeyDrop($tableName, ['question_answer_id']);
		$this->dropTable($tableName);
	}
}
