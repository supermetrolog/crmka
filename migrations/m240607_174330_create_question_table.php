<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%question}}`.
 */
class m240607_174330_create_question_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%question}}';
		$this->table($tableName, [
			'id'   => $this->primaryKey(),
			'text' => $this->text()->notNull(),
		], $this->timestamps(), $this->softDelete());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%question}}';
		$this->dropTable($tableName);
	}
}
