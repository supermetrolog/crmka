<?php

use app\kernel\console\Migration;

class m250922_133308_create_user_whatsapp_link_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%user_whatsapp_link}}';

		$this->table($tableName, [
			'id'                  => $this->primaryKey(),
			'user_id'             => $this->integer()->notNull(),
			'phone'               => $this->string(16)->notNull(),
			'whatsapp_profile_id' => $this->string(32)->notNull(),
			'first_name'          => $this->string(64)->null(),
			'full_name'           => $this->string(128)->null(),
			'push_name'           => $this->string(128)->null(),
			'revoked_at'          => $this->timestamp()->null()
		], $this->timestamps());

		$this->index($tableName, ['user_id']);

		$this->foreignKey($tableName, ['user_id'], '{{%user}}', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%user_whatsapp_link}}';

		$this->foreignKeyDrop($tableName, ['user_id']);

		$this->dropTable($tableName);
	}
}
