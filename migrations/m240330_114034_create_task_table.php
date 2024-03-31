<?php

use app\kernel\console\Migration;


/**
 * Handles the creation of table `{{%task}}`.
 */
class m240330_114034_create_task_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task}}';

		$this->table($tableName, [
			'id'      => $this->primaryKey(),
			'user_id' => $this->integer()->notNull(),
			'message' => $this->text()->notNull(),
			'status'  => $this->tinyInteger()->notNull(),
			'start'   => $this->timestamp(),
			'end'     => $this->timestamp(),
		], $this->morph('created_by'), $this->timestamps(), $this->softDelete());

		$this->index(
			$tableName,
			['user_id']
		);

		$this->index(
			$tableName,
			['created_by_type', 'created_by_id']
		);

		$this->foreignKey(
			$tableName,
			['user_id'],
			'user',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task}}';
		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->dropTable($tableName);
	}
}
