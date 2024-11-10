<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%answer_question_effect}}`.
 */
class m241110_134327_create_question_answer_effect_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%question_answer_effect}}';

		$this->table($tableName, [
			'id'                 => $this->primaryKey(),
			'question_answer_id' => $this->integer(),
			'effect_id'          => $this->integer(),
		]);

		$this->index(
			$tableName,
			['question_answer_id']
		);

		$this->foreignKey(
			$tableName,
			['question_answer_id'],
			'question_answer',
			['id']
		);

		$this->index(
			$tableName,
			['effect_id']
		);

		$this->foreignKey(
			$tableName,
			['effect_id'],
			'effect',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%answer_question_effect}}';

		$this->foreignKeyDrop($tableName, ['question_answer_id']);
		$this->foreignKeyDrop($tableName, ['effect_id']);
		$this->dropTable($tableName);
	}
}
