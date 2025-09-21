<?php

use app\kernel\console\Migration;

class m250916_141016_create_user_telegram_link_ticket_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%user_telegram_link_ticket}}';

		$this->table($tableName, [
			'id'          => $this->primaryKey(),
			'user_id'     => $this->integer()->notNull(),
			'code'        => $this->string(32)->notNull(),
			'expires_at'  => $this->timestamp()->notNull(),
			'consumed_at' => $this->timestamp()->null()
		], $this->timestamps());

		$this->index($tableName, ['user_id']);

		$this->foreignKey($tableName, ['user_id'], '{{%user}}', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%user_telegram_link_ticket}}';

		$this->foreignKeyDrop($tableName, ['user_id']);

		$this->dropTable($tableName);
	}
}
