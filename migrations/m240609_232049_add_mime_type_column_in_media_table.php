<?php

use app\kernel\console\Migration;

/**
 * Class m240609_232049_add_mime_type_column_in_media_table
 */
class m240609_232049_add_mime_type_column_in_media_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$table = 'media';
		$this->addColumn(
			$table,
			'mime_type',
			$this->string()->notNull()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$table = 'media';
		$this->dropColumn($table, 'mime_type');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m240609_232049_add_mime_type_column_in_media_table cannot be reverted.\n";

	return false;
	}
	*/
}