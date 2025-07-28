<?php

use app\kernel\console\Migration;

/**
 * Class m250727_172348_rename_entity_pinned_message_table
 */
class m250727_172348_rename_entity_pinned_message_table extends Migration
{
	public function safeUp()
	{
		$this->renameTable('entity_pinned_message', 'entity_message_link');
	}

	public function safeDown()
	{
		$this->renameTable('entity_message_link', 'entity_pinned_message');
	}
}