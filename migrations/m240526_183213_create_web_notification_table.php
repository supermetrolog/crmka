<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%web_notification}}`.
 */
class m240526_183213_create_web_notification_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%web_notification}}';

		$this->table($tableName, [
			'id'                   => $this->primaryKey(),
			'user_id'              => $this->integer()->notNull(),
			'user_notification_id' => $this->integer()->notNull(),
			'subject'              => $this->string()->notNull(),
			'message'              => $this->text()->notNull(),
			'viewed_at'            => $this->timestamp()->null()
		], $this->timestamps());

		$this->index($tableName, ['user_id']);
		$this->foreignKey($tableName, ['user_id'], 'user', ['id']);


		$this->index($tableName, ['user_notification_id']);
		$this->foreignKey($tableName, ['user_notification_id'], 'user_notification', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%web_notification}}';

		$this->foreignKeyDrop($tableName, ['user_notification_id']);
		$this->foreignKeyDrop($tableName, ['user_id']);

		$this->dropTable($tableName);
	}
}
