<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_task_tag}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%task}}`
 * - `{{%task_tag}}`
 */
class m240820_091703_create_junction_table_for_task_and_task_tag_tables extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task_task_tag}}';

		$this->table($tableName, [
			'id'          => $this->primaryKey(),
			'task_id'     => $this->integer(),
			'task_tag_id' => $this->integer(),
		], $this->timestamps(), $this->softDelete());

		$this->index(
			$tableName,
			['task_id']
		);

		$this->foreignKey(
			$tableName,
			['task_id'],
			'task',
			['id']
		);

		$this->index(
			$tableName,
			['task_tag_id']
		);

		$this->foreignKey(
			$tableName,
			['task_tag_id'],
			'task_tag',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task_task_tag}}';

		$this->foreignKeyDrop($tableName, ['task_id']);
		$this->foreignKeyDrop($tableName, ['task_tag_id']);
		$this->dropTable($tableName);
	}
}
