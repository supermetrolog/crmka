<?php

use app\kernel\console\Migration;

/**
 * Class m250117_094831_add_template_column_for_chat_member_message_table
 */
class m250117_094831_add_template_column_for_chat_member_message_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%chat_member_message}}';

		$this->addColumn($tableName, 'template', $this->string(32)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{

		$tableName = '{{%chat_member_message}}';

		$this->dropColumn($tableName, 'template');
	}
}