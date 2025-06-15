<?php

use app\kernel\console\Migration;

/**
 * Class m250615_085709_add_template_column_in_task_table
 */
class m250615_085709_add_type_column_in_task_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task}}';

		$this->addColumn($tableName, 'type', $this->string(32)->defaultValue('base')->notNull());

		$this->index($tableName, ['type']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task}}';

		$this->dropColumn($tableName, 'type');
	}
}