<?php

use app\kernel\console\Migration;

/**
 * Class m250727_171246_add_type_column_in_entity_pinned_message_column
 */
class m250727_171246_add_kind_column_in_entity_pinned_message_column extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%entity_pinned_message}}';

		$this->addColumn($tableName, 'kind', $this->string()->defaultValue('comment'));

		$this->index($tableName, ['kind']);
	}

	public function safeDown()
	{
		$tableName = '{{%entity_pinned_message}}';

		$this->dropColumn($tableName, 'kind');
	}
}