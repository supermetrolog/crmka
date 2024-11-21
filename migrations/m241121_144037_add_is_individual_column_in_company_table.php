<?php

use app\kernel\console\Migration;

/**
 * Class m241121_144037_add_is_individual_column_in_company_table
 */
class m241121_144037_add_is_individual_column_in_company_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = '{{%company}}';

		$this->addColumn($table, 'is_individual', $this->boolean()->defaultValue(false));
		$this->addColumn($table, 'individual_full_name', $this->string(255)->defaultValue(null));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = '{{%company}}';

		$this->dropColumn($table, 'is_individual');
		$this->dropColumn($table, 'individual_full_name');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m241121_144037_add_is_individual_column_in_company_table cannot be reverted.\n";

	return false;
	}
	*/
}