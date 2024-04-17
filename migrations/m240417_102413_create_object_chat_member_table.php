<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%object_chat_member}}`.
 */
class m240417_102413_create_object_chat_member_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%object_chat_member}}';
		$this->table($tableName, [
			'id'        => $this->primaryKey(),
			'object_id' => $this->integer()->notNull(),
			'type'      => $this->string()->notNull(),
		], $this->timestamps(), $this->morphCol('object'));

		$this->unique($tableName, ['object_id', 'type']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%object_chat_member}}';
		$this->dropTable($tableName);
	}
}
