<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_viewer}}`.
 */
class m240828_140323_create_task_observer_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task_observer}}';
		$this->table($tableName, [
			'id'            => $this->primaryKey(),
			'user_id'       => $this->integer()->notNull(),
			'task_id'       => $this->integer()->notNull(),
			'created_by_id' => $this->integer()->null(),
			'viewed_at'     => $this->timestamp()->null(),
		], $this->timestamps());

		$this->foreignKey(
			$tableName,
			['user_id'],
			'user',
			['id']
		);

		$this->foreignKey(
			$tableName,
			['task_id'],
			'task',
			['id']
		);

		$this->foreignKey(
			$tableName,
			['created_by_id'],
			'user',
			['id']
		);

		$this->index(
			$tableName,
			['user_id']
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
		$tableName = '{{%task_observer}}';
		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->foreignKeyDrop($tableName, ['task_id']);
		$this->foreignKeyDrop($tableName, ['created_by_id']);
		$this->dropTable($tableName);
	}
}
