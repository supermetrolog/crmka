<?php

use app\kernel\console\Migration;
use app\models\Notification\UserNotificationActionLog;

class m250904_050406_create_user_notification_action_log_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%user_notification_action_log}}';

		$this->table($tableName, [
			'id'                   => $this->primaryKey(),
			'user_notification_id' => $this->integer()->notNull(),
			'action_id'            => $this->integer()->notNull(),
			'user_id'              => $this->integer()->notNull(),
		], $this->softCreate('executed_at'));

		$this->addMorphColumn($tableName, UserNotificationActionLog::getMorphClass());

		$this->index($tableName, ['user_notification_id']);
		$this->index($tableName, ['action_id']);
		$this->index($tableName, ['user_id']);

		$this->foreignKey($tableName, ['user_notification_id'], '{{%user_notification}}', ['id']);
		$this->foreignKey($tableName, ['action_id'], '{{%user_notification_action}}', ['id']);
		$this->foreignKey($tableName, ['user_id'], '{{%user}}', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%user_notification_action_log}}';

		$this->foreignKeyDrop($tableName, ['user_notification_id']);
		$this->foreignKeyDrop($tableName, ['action_id']);
		$this->foreignKeyDrop($tableName, ['user_id']);

		$this->dropTable($tableName);
	}
}
