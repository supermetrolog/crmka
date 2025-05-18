<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_relation_entity}}`.
 */
class m250517_002235_create_task_relation_entity_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task_relation_entity}}';

		$this->table($tableName,
			[
				'id'            => $this->primaryKey(),
				'task_id'       => $this->integer()->notNull(),
				'created_by_id' => $this->integer()->null(),
				'deleted_by_id' => $this->integer()->null(),
				'comment'       => $this->string(255)->null(),
				'relation_type' => $this->string(16)->notNull()
			],
			$this->morph('entity'),
			$this->timestamps(),
			$this->softDelete()
		);

		$this->foreignKey($tableName, ['created_by_id'], 'user', ['id']);
		$this->foreignKey($tableName, ['deleted_by_id'], 'user', ['id']);
		$this->foreignKey($tableName, ['task_id'], 'task', ['id']);

		$this->index($tableName, ['created_by_id']);
		$this->index($tableName, ['deleted_by_id']);
		$this->index($tableName, ['task_id']);
		$this->index($tableName, ['entity_type', 'entity_id']);
		$this->index($tableName, ['task_id', 'entity_type', 'entity_id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task_relation_entity}}';

		$this->foreignKeyDrop($tableName, ['created_by_id']);
		$this->foreignKeyDrop($tableName, ['deleted_by_id']);
		$this->foreignKeyDrop($tableName, ['task_id']);

		$this->dropTable($tableName);
	}
}
