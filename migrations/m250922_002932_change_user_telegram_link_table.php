<?php

use app\kernel\console\Migration;

class m250922_002932_change_user_telegram_link_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%user_telegram_link}}';

		$this->alterColumn($tableName, 'username', $this->string(255)->null());
		$this->alterColumn($tableName, 'first_name', $this->string(255)->null());
		$this->alterColumn($tableName, 'last_name', $this->string(255)->null());
	}

	public function safeDown()
	{
		$tableName = '{{%user_telegram_link}}';

		$this->alterColumn($tableName, 'username', $this->string(255)->notNull());
		$this->alterColumn($tableName, 'first_name', $this->string(255)->notNull());
		$this->alterColumn($tableName, 'last_name', $this->string(255)->notNull());
	}
}