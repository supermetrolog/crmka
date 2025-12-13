<?php

use app\kernel\console\Migration;

class m251020_212522_create_attribute_tables extends Migration
{
	public function safeUp()
	{
		$attributeTable       = '{{%attribute}}';
		$attributeGroupTable  = '{{%attribute_group}}';
		$attributeOptionTable = '{{%attribute_option}}';

		$this->table($attributeTable, [
			'id'          => $this->primaryKey(),
			'kind'        => $this->string(64)->notNull(),
			'label'       => $this->string(64)->notNull(),
			'description' => $this->string(255)->null(),
			'value_type'  => $this->string(32)->notNull(),
			'input_type'  => $this->string(32)->notNull(),
		], $this->timestamps(), $this->softDelete());

		$this->table($attributeGroupTable, [
			'id'   => $this->primaryKey(),
			'name' => $this->string(64)->notNull()
		], $this->timestamps(), $this->softDelete());

		$this->table($attributeOptionTable, [
			'id'           => $this->primaryKey(),
			'attribute_id' => $this->integer()->notNull(),
			'label'        => $this->string(128)->null(),
			'value'        => $this->string(128)->notNull(),
			'sort_order'   => $this->integer()->notNull()->defaultValue(10)
		], $this->timestamps(), $this->softDelete());

		$this->foreignKey($attributeOptionTable, ['attribute_id'], $attributeTable, ['id']);

		$this->index($attributeOptionTable, ['attribute_id']);
	}

	public function safeDown()
	{
		$attributeTable       = '{{%attribute}}';
		$attributeGroupTable  = '{{%attribute_group}}';
		$attributeOptionTable = '{{%attribute_option}}';

		$this->foreignKeyDrop($attributeOptionTable, ['attribute_id']);

		$this->dropTable($attributeOptionTable);

		$this->dropTable($attributeGroupTable);
		$this->dropTable($attributeTable);
	}
}