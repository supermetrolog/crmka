<?php

use app\kernel\console\Migration;
use app\models\Notification\UserNotificationAction;

class m250904_041240_create_user_notification_action_table extends Migration
{
	/**
	 * @throws \yii\base\Exception
	 */
	public function safeUp()
	{
		$tableName = '{{%user_notification_action}}';

		$this->table($tableName, [
			'id'                   => $this->primaryKey(),
			'user_notification_id' => $this->integer()->notNull(),
			'code'                 => $this->string(32)->null(),
			'type'                 => $this->string(16)->notNull(),
			'label'                => $this->string(64)->notNull(),
			'order'                => $this->integer()->notNull(),
			'icon'                 => $this->string(64)->null(),
			'style'                => $this->string(32)->null(),
			'confirmation'         => $this->boolean()->notNull()->defaultValue(false),
			'expires_at'           => $this->timestamp()->null(),
			'payload'              => $this->json()->null()
		], $this->timestamps());

		$this->addMorphColumn($tableName, UserNotificationAction::getMorphClass());

		$this->index($tableName, ['user_notification_id']);

		$this->foreignKey($tableName, ['user_notification_id'], '{{%user_notification}}', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%user_notification_action}}';

		$this->foreignKeyDrop($tableName, ['user_notification_id']);

		$this->dropTable($tableName);
	}
}
