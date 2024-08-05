<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%mailing}}`.
 */
class m240526_161617_create_mailing_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%mailing}}';

		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'channel_id' => $this->integer()->notNull(),
			'subject'    => $this->string()->notNull(),
			'message'    => $this->text()->notNull(),
		], $this->morph('created_by'), $this->timestamps());

		$this->index($tableName, ['channel_id']);
		$this->index($tableName, ['created_by_type', 'created_by_id']);
		$this->foreignKey($tableName, ['channel_id'], 'notification_channel', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%mailing}}';

		$this->foreignKeyDrop($tableName, ['channel_id']);
		$this->dropTable($tableName);
	}
}
