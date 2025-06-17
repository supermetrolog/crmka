<?php

use app\kernel\console\Migration;

/**
 * Class m250617_233456_add_morph_column_in_c_industry_blocks_table
 */
class m250617_233456_add_morph_column_in_c_industry_blocks_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->db = Yii::$app->db_old;

		$this->addMorphColumn('c_industry_blocks');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->db = Yii::$app->db_old;

		$this->dropMorphColumn('c_industry_blocks');
	}
}