<?php

use app\kernel\console\Migration;
use app\models\CommercialOffer;
use app\models\OfferMix;
use app\models\Request;
use app\models\User\User;

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
		$this->addMorphColumn('user', User::getMorphClass());
		$this->addMorphColumn('request', Request::getMorphClass());

		$this->db = Yii::$app->db_old;

		$this->addMorphColumn('c_industry_offers_mix', OfferMix::getMorphClass());
		$this->addMorphColumn('c_industry_offers', CommercialOffer::getMorphClass());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('request');
		$this->dropMorphColumn('user');

		$this->db = Yii::$app->db_old;
		$this->dropMorphColumn('c_industry_offers_mix');
		$this->dropMorphColumn('c_industry_offers');
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