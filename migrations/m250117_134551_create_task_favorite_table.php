<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_favorite}}`.
 */
class m250117_134551_create_task_favorite_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task_favorite}}';

		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'task_id'    => $this->integer()->notNull(),
			'user_id'    => $this->integer()->notNull(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP),
			'prev_id'    => $this->integer()->null()
		], $this->softDelete());

		$this->index($tableName, ['task_id']);
		$this->index($tableName, ['user_id']);

		$this->foreignKey($tableName, ['task_id'], '{{%task}}', ['id']);
		$this->foreignKey($tableName, ['user_id'], '{{%user}}', ['id']);
		$this->foreignKey($tableName, ['prev_id'], $tableName, ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task_favorite}}';

		$this->foreignKeyDrop($tableName, ['task_id']);
		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->foreignKeyDrop($tableName, ['prev_id']);

		$this->dropTable($tableName);
	}
}
