<?php

use app\kernel\console\Migration;

class m250907_195846_add_acted_at_column_in_user_notification_table extends Migration
{
	public function safeUp(): void
	{
		$tableName = "{{%user_notification}}";

		$this->addColumn($tableName, 'acted_at', $this->timestamp()->null());
	}

	public function safeDown(): void
	{
		$tableName = "{{%user_notification}}";

		$this->dropColumn($tableName, 'acted_at');
	}
}