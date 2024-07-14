<?php

use app\kernel\console\Migration;

/**
 * Class m240714_093404_set_null_preview_equipment_table
 */
class m240714_093404_set_null_preview_equipment_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%equipment}}';

		$this->alterColumn($tableName, 'preview_id', $this->integer()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%equipment}}';

		$this->alterColumn($tableName, 'preview_id', $this->integer()->notNull());
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240714_093404_set_null_preview_equipment_table cannot be reverted.\n";

	return false;
	}
	*/
}