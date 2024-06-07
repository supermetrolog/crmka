<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%question_answer}}`.
 */
class m240607_204221_create_question_answer_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%question_answer}}';
		$this->table($tableName, [
			'id'       => $this->primaryKey(),
			'field_id' => $this->integer()->notNull(),
			'category' => $this->tinyInteger()->notNull(),
			'value'    => $this->string()->null(),
		], $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['field_id']);

		$this->foreignKey($tableName, ['field_id'], 'field', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%question_answer}}';
		
		$this->foreignKeyDrop($tableName, ['field_id']);
		$this->dropTable($tableName);
	}
}
