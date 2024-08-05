<?php

use app\kernel\console\Migration;

/**
 * Class m240519_132442_add_morph_column_in_alert_table
 */
class m240519_132442_add_morph_column_in_alert_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('alert');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('alert');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240519_132442_add_morph_column_in_alert_table cannot be reverted.\n";

	return false;
	}
	*/
}