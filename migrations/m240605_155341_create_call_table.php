<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%call}}`.
 */
class m240605_155341_create_call_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%call}}';
		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'user_id'    => $this->integer()->notNull(),
			'contact_id' => $this->integer()->null(),
		], $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['user_id']);
		$this->index($tableName, ['contact_id']);

		$this->foreignKey(
			$tableName,
			['user_id'],
			'user',
			['id']
		);

		$this->foreignKey(
			$tableName,
			['contact_id'],
			'contact',
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%call}}';

		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->foreignKeyDrop($tableName, ['contact_id']);
		$this->dropTable($tableName);
	}
}
