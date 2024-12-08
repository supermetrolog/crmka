<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_event}}`.
 */
class m241207_092006_create_task_event_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task_event}}';

		$this->table($tableName, [
			'id'              => $this->primaryKey(),
			'task_history_id' => $this->integer()->notNull(),
			'event_type'      => $this->string(32)->notNull(),
			'created_at'      => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP)
		]);

		$this->index($tableName, ['task_history_id']);

		$this->foreignKey($tableName, ['task_history_id'], '{{%task_history}}', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task_event}}';

		$this->foreignKeyDrop($tableName, ['task_history_id']);
		$this->dropTable($tableName);
	}
}
