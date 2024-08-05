<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%notification_channel}}`.
 */
class m240525_081528_create_notification_channel_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%notification_channel}}';
		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'name'       => $this->string()->notNull(),
			'slug'       => $this->string()->notNull(),
			'is_enabled' => $this->boolean()->notNull()->defaultValue(false)
		], $this->timestamps());

		$this->unique($tableName, ['slug']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%notification_channel}}';
		$this->dropTable($tableName);
	}
}
