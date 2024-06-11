<?php

use app\kernel\console\Migration;

/**
 * Class m240609_225853_add_morph_column_in_user_notification_table
 */
class m240609_225853_add_morph_column_in_user_notification_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('user_notification');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('user_notification');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240609_225853_add_morph_column_in_user_notification_table cannot be reverted.\n";

	return false;
	}
	*/
}