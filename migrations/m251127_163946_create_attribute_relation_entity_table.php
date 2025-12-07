<?php

use app\kernel\console\Migration;

class m251127_163946_create_attribute_relation_entity_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%attribute_relation_entity}}';

		$this->table($tableName, [
			'id'           => $this->primaryKey(),
			'attribute_id' => $this->integer()->notNull(),
			'entity_id'    => $this->integer()->notNull(),
			'entity_type'  => $this->string()->notNull(),
		], $this->timestamps(), $this->softDelete());

		$this->foreignKey($tableName, ['attribute_id'], '{{%attribute}}', ['id']);
		$this->index($tableName, ['attribute_id']);
	}

	public function safeDown()
	{
		$tableName = '{{%attribute_relation_entity}}';

		$this->foreignKeyDrop($tableName, ['attribute_id']);
		$this->indexDrop($tableName, ['attribute_id']);

		$this->dropTable($tableName);
	}
}
