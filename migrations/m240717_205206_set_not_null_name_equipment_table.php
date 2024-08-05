<?php

use app\kernel\console\Migration;

/**
 * Class m240717_205206_set_not_null_name_equipment_table
 */
class m240717_205206_set_not_null_name_equipment_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%equipment}}';

		$this->alterColumn($tableName, 'name', $this->string(60)->notNull());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%equipment}}';

		$this->alterColumn($tableName, 'name', $this->string(60)->null());
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240717_205206_set_not_null_name_equipment_table cannot be reverted.\n";

	return false;
	}
	*/
}