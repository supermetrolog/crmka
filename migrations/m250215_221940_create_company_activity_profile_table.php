<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%company_activity_profile}}`.
 */
class m250215_221940_create_company_activity_profile_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%company_activity_profile}}';
		$this->table($tableName, [
			'id'                  => $this->primaryKey(),
			'company_id'          => $this->integer()->notNull(),
			'activity_profile_id' => $this->tinyInteger()->notNull(),
		]);

		$this->index($tableName, ['company_id']);
		$this->index($tableName, ['activity_profile_id']);

		$this->foreignKey($tableName, ['company_id'], '{{%company}}', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%company_activity_profile}}';
		$this->dropTable($tableName);
	}
}
