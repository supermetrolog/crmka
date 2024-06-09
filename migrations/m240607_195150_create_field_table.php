<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%field}}`.
 */
class m240607_195150_create_field_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%field}}';
		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'field_type' => $this->string()->notNull(),
			'type'       => $this->string()->notNull(),
		], $this->timestamps(), $this->softDelete());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%field}}';
		$this->dropTable($tableName);
	}
}
