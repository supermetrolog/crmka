<?php

use app\kernel\console\Migration;

class m251020_213631_create_attribute_value_tables extends Migration
{
	public function safeUp()
	{
		$attributeRuleTable       = '{{%attribute_rule}}';
		$attributeValueTable      = '{{%attribute_value}}';
		$attributeValueMediaTable = '{{%attribute_value_media}}';

		$this->table($attributeRuleTable, [
			'id'                 => $this->primaryKey(),
			'attribute_id'       => $this->integer()->notNull(),
			'attribute_group_id' => $this->integer()->null(),
			'entity_type'        => $this->string(64)->notNull(),
			'is_required'        => $this->boolean()->defaultValue(false),
			'is_inheritable'     => $this->boolean()->defaultValue(false),
			'is_editable'        => $this->boolean()->defaultValue(true),
			'status'             => $this->string(32)->notNull(),
			'sort_order'         => $this->integer()->notNull()->defaultValue(10)
		], $this->timestamps(), $this->softDelete());

		$this->foreignKey($attributeRuleTable, ['attribute_id'], '{{%attribute}}', ['id']);
		$this->foreignKey($attributeRuleTable, ['attribute_group_id'], '{{%attribute_group}}', ['id']);

		$this->index($attributeRuleTable, ['attribute_id']);
		$this->index($attributeRuleTable, ['attribute_group_id']);

		$this->table($attributeValueTable, [
			'id'           => $this->primaryKey(),
			'attribute_id' => $this->integer()->notNull(),
			'entity_type'  => $this->string(64)->notNull(),
			'entity_id'    => $this->integer()->notNull(),
			'value'        => $this->text()->null(),
		], $this->timestamps(), $this->softDelete());

		$this->foreignKey($attributeValueTable, ['attribute_id'], '{{%attribute}}', ['id']);

		$this->index($attributeValueTable, ['attribute_id']);

		$this->table($attributeValueMediaTable, [
			'id'           => $this->primaryKey(),
			'attribute_id' => $this->integer()->notNull(),
			'entity_type'  => $this->string(64)->notNull(),
			'entity_id'    => $this->integer()->notNull(),
			'media_id'     => $this->integer()->notNull(),
		], $this->timestamps(), $this->softDelete());

		$this->foreignKey($attributeValueMediaTable, ['attribute_id'], '{{%attribute}}', ['id']);
		$this->foreignKey($attributeValueMediaTable, ['media_id'], '{{%media}}', ['id']);

		$this->index($attributeValueMediaTable, ['attribute_id']);
		$this->index($attributeValueMediaTable, ['media_id']);
	}

	public function safeDown()
	{
		$attributeRuleTable       = '{{%attribute_rule}}';
		$attributeValueTable      = '{{%attribute_value}}';
		$attributeValueMediaTable = '{{%attribute_value_media}}';

		$this->foreignKeyDrop($attributeRuleTable, ['attribute_id']);
		$this->foreignKeyDrop($attributeRuleTable, ['attribute_group_id']);

		$this->dropTable($attributeRuleTable);

		$this->foreignKeyDrop($attributeValueTable, ['attribute_id']);

		$this->dropTable($attributeValueTable);

		$this->foreignKeyDrop($attributeValueMediaTable, ['attribute_id']);
		$this->foreignKeyDrop($attributeValueMediaTable, ['media_id']);

		$this->dropTable($attributeValueMediaTable);
	}
}