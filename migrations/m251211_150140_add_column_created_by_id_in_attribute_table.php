<?php

use app\kernel\console\Migration;

class m251211_150140_add_column_created_by_id_in_attribute_table extends Migration
{
	public function safeUp()
	{
		$table = '{{%attribute}}';

		$this->addColumn($table, 'created_by_id', $this->integer());
		$this->foreignKey($table, ['created_by_id'], '{{%user}}', ['id']);
	}

	public function safeDown()
	{
		$table = '{{%attribute}}';

		$this->foreignKeyDrop($table, ['created_by_id']);
		$this->dropColumn($table, 'created_by_id');
	}
}