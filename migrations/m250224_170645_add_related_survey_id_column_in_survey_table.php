<?php

use app\kernel\console\Migration;

/**
 * Class m250224_170645_add_related_survey_id_column_in_survey_table
 */
class m250224_170645_add_related_survey_id_column_in_survey_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%survey}}';

		$this->addColumn($tableName, 'related_survey_id', $this->integer()->null());

		$this->index($tableName, ['related_survey_id']);

		$this->foreignKey(
			$tableName,
			['related_survey_id'],
			$tableName,
			['id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%survey}}';

		$this->foreignKeyDrop($tableName, ['related_survey_id']);
		$this->dropColumn($tableName, 'related_survey_id');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250224_170645_add_related_survey_id_column_in_survey_table cannot be reverted.\n";

	return false;
	}
	*/
}