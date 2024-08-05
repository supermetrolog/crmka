<?php

use app\kernel\console\Migration;

/**
* Class m240713_221431_set_null_description_equipment_table
*/
class m240713_221431_set_null_description_equipment_table extends Migration
{
	/**
	* {@inheritdoc}
	*/
	public function safeUp()
	{
		$tableName = '{{%equipment}}';

		$this->alterColumn($tableName, 'description', $this->text()->null());
	}

	/**
	* {@inheritdoc}
	*/
	public function safeDown()
	{
		$tableName = '{{%equipment}}';

		$this->alterColumn($tableName, 'description', $this->text()->notNull());
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240713_221431_set_null_description_equipment_table cannot be reverted.\n";

	return false;
	}
	*/
}