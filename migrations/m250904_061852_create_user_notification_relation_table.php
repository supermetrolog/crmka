<?php

use app\kernel\console\Migration;
use app\models\Notification\UserNotificationRelation;

class m250904_061852_create_user_notification_relation_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%user_notification_relation}}';

		$this->table($tableName, [
			'id'              => $this->primaryKey(),
			'notification_id' => $this->integer()->notNull()
		], $this->timestamps(), $this->morph('entity'));

		$this->addMorphColumn($tableName, UserNotificationRelation::getMorphClass());

		$this->index($tableName, ['notification_id']);
		$this->index($tableName, ['entity_type', 'entity_id']);

		$this->foreignKey($tableName, ['notification_id'], '{{%user_notification}}', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%user_notification_relation}}';

		$this->foreignKeyDrop($tableName, ['notification_id']);

		$this->dropTable($tableName);
	}
}
