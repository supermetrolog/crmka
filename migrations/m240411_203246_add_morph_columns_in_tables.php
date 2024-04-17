<?php

use app\kernel\console\Migration;
use app\models\CommercialOffer;
use app\models\OfferMix;
use app\models\Request;
use app\models\User;

/**
 * Class m240411_203246_add_morph_columns_in_tables
 */
class m240411_203246_add_morph_columns_in_tables extends Migration
{
	/**
	 * {@inheritdoc}
	 * @throws \yii\base\ErrorException
	 */
	public function safeUp()
	{
		echo $this->db->dsn . PHP_EOL;

//		$this->addMorphColumn('user', User::getMorphClass());
//		$this->addMorphColumn('request', Request::getMorphClass());

		$this->db = Yii::$app->db_old;

		echo $this->db->dsn . PHP_EOL;

		$this->addMorphColumn('c_industry_offers_mix', OfferMix::getMorphClass());
		$this->addMorphColumn('c_industry_offers', CommercialOffer::getMorphClass());
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
		$this->dropMorphColumns('c_industry_offers');
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