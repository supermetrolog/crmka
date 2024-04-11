<?php

use app\kernel\console\Migration;

/**
 * Class m240411_203246_add_morph_columns_in_tables
 */
class m240411_203246_add_morph_columns_in_tables extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('user', 'user');
		$this->addMorphColumn('request', 'request');

		$this->db = Yii::$app->db_old;

		$this->addMorphColumn('c_industry_offers_mix', 'c_industry_offers_mix');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumns('request');
		$this->dropMorphColumns('user');

		$this->db = Yii::$app->db_old;
		$this->dropMorphColumns('c_industry_offers_mix');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240411_203246_add_morph_columns_in_tables cannot be reverted.\n";

	return false;
	}
	*/
}