<?php

use app\kernel\console\Migration;

/**
 * Class m250604_210953_add_morph_column_in_c_industry_table
 */
class m250604_210953_add_morph_column_in_c_industry_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->db = Yii::$app->db_old;

		$this->addMorphColumn('c_industry');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->db = Yii::$app->db_old;

		$this->dropMorphColumn('c_industry');
	}
}