<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%alert}}`.
 */
class m240513_192034_create_alert_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%alert}}';
		$this->table($tableName, [
			'id'      => $this->primaryKey(),
			'user_id' => $this->integer()->notNull(),
			'message' => $this->text()->notNull(),
		], $this->morph('created_by'), $this->timestamps(), $this->softDelete(), $this->morphCol('alert'));


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
		$tableName = '{{%alert}}';
		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->dropTable($tableName);
	}
}
