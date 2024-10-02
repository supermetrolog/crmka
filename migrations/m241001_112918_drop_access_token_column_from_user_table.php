<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%user}}`.
 */
class m241001_112918_drop_access_token_column_from_user_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = '{{%user}}';

		$this->dropColumn($table, 'access_token');
		$this->dropColumn($table, 'auth_key');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = '{{%user}}';

		$this->addColumn($table, 'access_token', $this->string()->defaultValue(null));
		$this->addColumn($table, 'auth_key', $this->string(32)->notNull());
	}
}
