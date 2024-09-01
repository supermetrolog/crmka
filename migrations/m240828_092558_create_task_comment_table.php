<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_comment}}`.
 */
class m240828_092558_create_task_comment_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task_comment}}';
		$this->table($tableName, [
			'id'            => $this->primaryKey(),
			'message'       => $this->string()->notNull(),
			'created_by_id' => $this->integer()->notNull(),
			'task_id'       => $this->integer()->notNull()
		], $this->timestamps(), $this->softDelete());

		$this->foreignKey(
			$tableName,
			['created_by_id'],
			'user',
			['id']
		);

		$this->foreignKey(
			$tableName,
			['task_id'],
			'task',
			['id']
		);

		$this->index(
			$tableName,
			['created_by_id']
		);

		$this->index(
			$tableName,
			['task_id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task_comment}}';
		$this->foreignKeyDrop($tableName, ['created_by_id']);
		$this->foreignKeyDrop($tableName, ['task_id']);
		$this->dropTable($tableName);
	}
}
