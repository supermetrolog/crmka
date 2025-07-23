<?php

use app\kernel\console\Migration;

/**
 * Class m250723_114806_add_restrict_ip_login_column_in_user_table
 */
class m250723_114806_add_restrict_ip_login_column_in_user_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%user}}';

		$this->addColumn($tableName, 'restrict_ip_login', $this->boolean()->defaultValue(false)->notNull());

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%user}}';

		$this->dropColumn($tableName, 'restrict_ip_login');
	}
}