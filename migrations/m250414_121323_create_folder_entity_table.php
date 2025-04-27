<?php

use app\kernel\console\Migration;

class m250414_121323_create_folder_entity_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%folder_entity}}';

		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'folder_id'  => $this->integer()->notNull(),
			'sort_order' => $this->float(3)->notNull()->defaultValue(100),
			'created_at' => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP),
		], $this->morph('entity'));

		$this->index($tableName, ['folder_id']);
		$this->index($tableName, ['entity_id']);
		$this->index($tableName, ['folder_id', 'sort_order']);

		$this->foreignKey(
			$tableName,
			['folder_id'],
			'folder',
			['id']
		);
	}

	public function safeDown()
	{
		$tableName = '{{%folder_entity}}';

		$this->foreignKeyDrop($tableName, ['folder_id']);

		$this->dropTable($tableName);
	}
}
