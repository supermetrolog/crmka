<?php

use app\kernel\console\Migration;

class m251128_154135_create_location_relation_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%location_relation}}';
		$this->table($tableName, [
			'id'                    => $this->primaryKey(),
			'owner_location_id'     => $this->integer()->notNull(),
			'child_location_id'     => $this->integer()->notNull(),
			'type'                  => $this->string(64)->null(),
			'strength'              => $this->integer()->notNull(),
			'is_included_to_search' => $this->boolean()->notNull()->defaultValue(true),
		]);

		$this->foreignKey($tableName, ['owner_location_id'], '{{%location}}', ['id']);
		$this->foreignKey($tableName, ['child_location_id'], '{{%location}}', ['id']);

		$this->index($tableName, ['owner_location_id']);
		$this->index($tableName, ['child_location_id']);
	}

	public function safeDown()
	{
		$tableName = '{{%location_relation}}';

		$this->foreignKeyDrop($tableName, ['owner_location_id']);
		$this->foreignKeyDrop($tableName, ['child_location_id']);

		$this->dropTable($tableName);
	}
}
