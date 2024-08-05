<?php

use app\kernel\console\Migration;

/**
 * Class m240504_105334_add_morph_columns
 */
class m240504_105334_add_morph_columns extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addMorphColumn('chat_member_message');
		$this->addMorphColumn('task');
		$this->addMorphColumn('contact');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropMorphColumn('contact');
		$this->dropMorphColumn('task');
		$this->dropMorphColumn('chat_member_message');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240504_105334_add_morph_columns cannot be reverted.\n";

	return false;
	}
	*/
}