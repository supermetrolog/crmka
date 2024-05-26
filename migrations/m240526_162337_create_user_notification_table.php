<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%user_notification}}`.
 */
class m240526_162337_create_user_notification_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%user_notification}}';

		$this->table($tableName, [
			'id'          => $this->primaryKey(),
			'mailing_id'  => $this->integer()->notNull(),
			'user_id'     => $this->integer()->notNull(),
			'notified_at' => $this->timestamp()->null()
		], $this->timestamps());

		$this->foreignKey($tableName, ['mailing_id'], 'mailing', ['id']);
		$this->foreignKey($tableName, ['user_id'], 'user', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%user_notification}}';

		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->foreignKeyDrop($tableName, ['mailing_id']);
		$this->dropTable($tableName);
	}
}
