<?php

use app\kernel\console\Migration;

/**
 * Class m250403_052550_add_updated_at_column_in_contact_comment_table
 */
class m250403_052550_add_updated_at_column_in_contact_comment_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%contact_comment}}';

		$this->addColumn($tableName, 'updated_at', $this->timestamp()->defaultValue(null));
		$this->addColumn($tableName, 'deleted_at', $this->timestamp()->defaultValue(null));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%contact_comment}}';

		$this->dropColumn($tableName, 'updated_at');
		$this->dropColumn($tableName, 'deleted_at');
	}

	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
	echo "m250403_052550_add_updated_at_column_in_contact_comment_table cannot be reverted.\n";

	return false;
	}
	*/
}