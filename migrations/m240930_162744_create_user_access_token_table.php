<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%user_access_token}}`.
 */
class m240930_162744_create_user_access_token_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%user_access_token}}';
		$this->table($tableName, [
			'id'           => $this->primaryKey(),
			'user_id'      => $this->integer()->notNull(),
			'access_token' => $this->string()->notNull(),
			'expires_at'   => $this->timestamp()->notNull(),
			'ip'           => $this->string(19),
			'user_agent'   => $this->string(),
		], $this->timestamps(), $this->softDelete());

		$this->foreignKey($tableName, ['user_id'], 'user', ['id']);

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%user_access_token}}';
		$this->dropTable($tableName);
	}
}
