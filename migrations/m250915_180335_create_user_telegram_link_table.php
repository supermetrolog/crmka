<?php

use app\kernel\console\Migration;

class m250915_180335_create_user_telegram_link_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%user_telegram_link}}';

		$this->table($tableName, [
			'id'               => $this->primaryKey(),
			'user_id'          => $this->integer()->notNull(),
			'telegram_user_id' => $this->bigInteger()->null(),
			'chat_id'          => $this->bigInteger()->null(),
			'username'         => $this->string()->notNull(),
			'first_name'       => $this->string()->notNull(),
			'last_name'        => $this->string()->notNull(),
			'revoked_at'       => $this->timestamp()->null(),
			'is_enabled'       => $this->boolean()->notNull()->defaultValue(true),
		], $this->timestamps());

		$this->index($tableName, ['user_id']);
		$this->index($tableName, ['telegram_user_id']);

		$this->foreignKey($tableName, ['user_id'], '{{%user}}', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%user_telegram_link}}';

		$this->foreignKeyDrop($tableName, ['user_id']);

		$this->dropTable($tableName);
	}
}
