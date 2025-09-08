<?php

use app\kernel\console\Migration;
use app\models\Notification\UserNotificationTemplate;

class m250904_030635_create_user_notification_template_table extends Migration
{
	public function safeUp(): void
	{
		$tableName = '{{%user_notification_template}}';

		$this->table($tableName, [
			'id'        => $this->primaryKey(),
			'kind'      => $this->string(32)->notNull()->unique(),
			'priority'  => $this->string(16)->notNull(),
			'category'  => $this->string(32)->notNull(),
			'is_active' => $this->boolean()->notNull()->defaultValue(true),
		], $this->timestamps());

		$this->addMorphColumn($tableName, UserNotificationTemplate::getMorphClass());

		$this->index($tableName, ['kind']);
		$this->index($tableName, ['category']);
	}

	public function safeDown(): void
	{
		$tableName = '{{%user_notification_template}}';

		$this->dropTable($tableName);
	}
}
