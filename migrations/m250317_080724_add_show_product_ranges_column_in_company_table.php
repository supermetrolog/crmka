<?php

use app\kernel\console\Migration;

/**
 * Class m250317_080724_add_show_product_ranges_column_in_company_table
 */
class m250317_080724_add_show_product_ranges_column_in_company_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = "{{%company}}";

		$this->addColumn($table, 'show_product_ranges', $this->boolean()->defaultValue(true));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = "{{%company}}";

		$this->dropColumn($table, 'show_product_ranges');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250317_080724_add_show_product_ranges_column_in_company_table cannot be reverted.\n";

	return false;
	}
	*/
}