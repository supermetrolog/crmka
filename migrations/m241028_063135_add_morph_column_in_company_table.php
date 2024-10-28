<?php

use app\kernel\console\Migration;

/**
 * Class m241028_063135_add_morph_column_in_company_table
 */
class m241028_063135_add_morph_column_in_company_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('company');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('company');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m241028_063135_add_morph_column_in_company_table cannot be reverted.\n";

	return false;
	}
	*/
}