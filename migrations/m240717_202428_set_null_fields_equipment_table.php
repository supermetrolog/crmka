<?php

use app\kernel\console\Migration;

/**
 * Class m240717_202428_set_null_fields_equipment_table
 */
class m240717_202428_set_null_fields_equipment_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%equipment}}';

		$this->alterColumn($tableName, 'availability', $this->integer()->null());
		$this->alterColumn($tableName, 'delivery', $this->integer()->null());
		$this->alterColumn($tableName, 'price', $this->integer()->null());
		$this->alterColumn($tableName, 'benefit', $this->integer()->null());
		$this->alterColumn($tableName, 'tax', $this->integer()->null());
		$this->alterColumn($tableName, 'count', $this->integer()->null());
		$this->alterColumn($tableName, 'status', $this->integer()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%equipment}}';

		$this->alterColumn($tableName, 'availability', $this->integer()->notNull());
		$this->alterColumn($tableName, 'delivery', $this->integer()->notNull());
		$this->alterColumn($tableName, 'price', $this->integer()->notNull());
		$this->alterColumn($tableName, 'benefit', $this->integer()->notNull());
		$this->alterColumn($tableName, 'tax', $this->integer()->notNull());
		$this->alterColumn($tableName, 'count', $this->integer()->notNull());
		$this->alterColumn($tableName, 'status', $this->integer()->notNull());
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240717_202428_set_null_fields_equipment_table cannot be reverted.\n";

	return false;
	}
	*/
}