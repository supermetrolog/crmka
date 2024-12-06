<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_event}}`.
 */
class m241206_052705_create_task_event_table extends Migration
{
	/**
	 * {@inheritdoc}
	 * @throws \yii\base\Exception
	 */
	public function safeUp()
	{
		$tableName = '{{%task_event}}';

		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'task_id'    => $this->integer()->notNull(),
			'event_type' => $this->string(32)->notNull(),
			'old_value'  => $this->json()->null(),
			'new_value'  => $this->json()->null(),
			'context'    => $this->json()->null(),
			'batch_id'   => $this->string(32)->null(),
		], $this->timestamps(), $this->morph('initiator'));

		$this->index($tableName, ['task_id']);
		$this->index($tableName, ['batch_id']);
		$this->index($tableName, ['event_type']);
		$this->index($tableName, ['initiator_id', 'initiator_type']);

		$this->foreignKey($tableName, ['task_id'], '{{%task}}', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task_event}}';

		$this->foreignKeyDrop($tableName, ['task_id']);
		$this->dropTable($tableName);
	}
}
