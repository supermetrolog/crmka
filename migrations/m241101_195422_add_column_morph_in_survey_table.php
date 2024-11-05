<?php

use app\kernel\console\Migration;

/**
 * Class m241101_195422_add_column_morph_in_survey_table
 */
class m241101_195422_add_column_morph_in_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = "{{%survey}}";

		$this->addMorphColumn($table);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = "{{%survey}}";

		$this->dropMorphColumn($table);

		return false;
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m241101_195422_add_column_morph_in_survey_table cannot be reverted.\n";

	return false;
	}
	*/
}