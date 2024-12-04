<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%user_activity}}`.
 */
class m241203_165002_create_user_activity_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%user_activity}}';

		$this->table($tableName, [
			'id'               => $this->primaryKey(),
			'user_id'          => $this->integer()->notNull(),
			'ip'               => $this->string(15)->notNull(),
			'user_agent'       => $this->string(1024)->notNull(),
			'last_page'        => $this->string(128)->null(),
			'started_at'       => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP),
			'last_activity_at' => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP),
		]);

		$this->index($tableName, ['user_id']);
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
		$tableName = '{{%user_activity}}';

		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->dropTable($tableName);
	}
}
