<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%relations}}`.
 */
class m240504_102108_create_relations_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%relation}}';
		$this->table(
			$tableName,
			[
				'id' => $this->primaryKey(),
			],
			$this->morph('first'),
			$this->morph('second'),
			$this->timestamps()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%relation}}';
		$this->dropTable($tableName);
	}
}
