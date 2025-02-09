<?php

use app\kernel\console\Migration;

/**
 * Class m250209_121232_add_template_column_for_question_table
 */
class m250209_121232_add_template_column_for_question_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%question}}';

		$this->addColumn($tableName, 'template', $this->string(32)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%question}}';

		$this->dropColumn($tableName, 'template');

		return false;
	}
}