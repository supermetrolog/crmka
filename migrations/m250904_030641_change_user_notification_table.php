<?php

use app\kernel\console\Migration;
use app\models\Notification\UserNotification;

class m250904_030641_change_user_notification_table extends Migration
{
	public function safeUp(): void
	{
		$tableName = '{{%user_notification}}';

		$this->addColumns($tableName, [
			'template_id' => $this->integer()->null(),
			'expires_at'  => $this->timestamp()->null()
		]);

		$this->addMorphColumn($tableName, UserNotification::getMorphClass());

		$this->foreignKey($tableName, ['template_id'], 'user_notification_template', ['id']);
		$this->index($tableName, ['template_id']);
	}

	public function safeDown(): void
	{
		$tableName = '{{%user_notification}}';

		$this->foreignKeyDrop($tableName, ['template_id']);

		$this->dropColumns($tableName, ['template_id', 'expires_at']);
	}
}