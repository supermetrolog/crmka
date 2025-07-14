<?php

use app\kernel\console\Migration;

/**
 * Class m250709_205512_add_phone_id_in_call_table
 */
class m250709_205512_add_phone_id_in_call_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%call}}';

		$this->addColumn($tableName, 'phone_id', $this->integer()->null());

		$this->foreignKey($tableName, ['phone_id'], 'phone', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%call}}';

		$this->dropColumn($tableName, 'phone_id');
	}
}