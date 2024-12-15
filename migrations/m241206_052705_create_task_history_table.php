<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_event}}`.
 */
class m241206_052705_create_task_history_table extends Migration
{
	/**
	 * {@inheritdoc}
	 * @throws \yii\base\Exception
	 */
	public function safeUp()
	{
		$tableName = '{{%task_history}}';

		$this->table($tableName, [
			'id'            => $this->primaryKey(),
			'user_id'       => $this->integer()->notNull(),
			'message'       => $this->text()->notNull(),
			'status'        => $this->tinyInteger()->notNull(),
			'start'         => $this->timestamp(),
			'end'           => $this->timestamp(),
			'impossible_to' => $this->timestamp()->null(),

			'task_id' => $this->integer()->notNull(),
			'prev_id' => $this->integer()->null(),
			'state'   => $this->json()->null(),
		], $this->morph('created_by'), $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['user_id']);
		$this->index($tableName, ['created_by_type', 'created_by_id']);
		$this->index($tableName, ['task_id']);
		$this->index($tableName, ['prev_id']);

		$this->foreignKey($tableName, ['task_id'], '{{%task}}', ['id']);
		$this->foreignKey($tableName, ['user_id'], '{{%user}}', ['id']);
		$this->foreignKey($tableName, ['prev_id'], '{{%task_history}}', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task_history}}';

		$this->foreignKeyDrop($tableName, ['task_id']);
		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->foreignKeyDrop($tableName, ['prev_id']);
		$this->dropTable($tableName);
	}
}
