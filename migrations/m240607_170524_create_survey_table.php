<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%survey}}`.
 */
class m240607_170524_create_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%survey}}';
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
		$tableName = '{{%survey}}';

		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->foreignKeyDrop($tableName, ['contact_id']);
		$this->dropTable($tableName);
	}
}
