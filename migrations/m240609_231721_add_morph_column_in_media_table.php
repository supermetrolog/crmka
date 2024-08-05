<?php

use app\kernel\console\Migration;

/**
 * Class m240609_231721_add_morph_column_in_media_table
 */
class m240609_231721_add_morph_column_in_media_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('media');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('media');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240609_231721_add_morph_column_in_media_table cannot be reverted.\n";

	return false;
	}
	*/
}