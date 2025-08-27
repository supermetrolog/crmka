<?php

use app\kernel\console\Migration;

class m250827_115018_create_letter_contact_event_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%letter_contact_event}}';

		$this->table($tableName, [
			'id'                => $this->primaryKey(),
			'letter_contact_id' => $this->integer()->notNull(),
			'event_type'        => $this->string(16)->notNull(),
			'ip'                => $this->string(15)->notNull(),
			'user_agent'        => $this->string(1024)->notNull(),
			'created_at'        => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP)
		]);

		$this->index($tableName, ['letter_contact_id']);
		$this->index($tableName, ['event_type']);

		$this->foreignKey($tableName, ['letter_contact_id'], 'letter_contact', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%letter_contact_event}}';

		$this->foreignKeyDrop($tableName, ['letter_contact_id']);

		$this->dropTable($tableName);
	}
}
