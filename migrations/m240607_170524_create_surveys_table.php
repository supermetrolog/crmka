<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%surveys}}`.
 */
class m240607_170524_create_surveys_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%surveys}}';
		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'user_id'    => $this->integer()->notNull(),
			'contact_id' => $this->integer()->notNull(),
		], $this->timestamps());

		$this->index($tableName, ['user_id']);
		$this->index($tableName, ['contact_id']);

		$this->foreignKey($tableName, ['user_id'], 'user', ['id']);
		$this->foreignKey($tableName, ['contact_id'], 'contact', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%surveys}}';

		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->foreignKeyDrop($tableName, ['contact_id']);
		$this->dropTable($tableName);
	}
}
