<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%company_activity_group}}`.
 */
class m250215_221946_create_company_activity_group_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%company_activity_group}}';

		$this->table($tableName, [
			'id'                => $this->primaryKey(),
			'company_id'        => $this->integer()->notNull(),
			'activity_group_id' => $this->tinyInteger()->notNull(),
		]);

		$this->index($tableName, ['company_id']);
		$this->index($tableName, ['activity_group_id']);

		$this->foreignKey($tableName, ['company_id'], '{{%company}}', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%company_activity_group}}';
		$this->dropTable($tableName);
	}
}
