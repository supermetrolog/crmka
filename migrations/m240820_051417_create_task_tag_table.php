<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%task_tag}}`.
 */
class m240820_051417_create_task_tag_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%task_tag}}';
		$this->table($tableName,
			[
				'id'          => $this->primaryKey(),
				'name'        => $this->string()->notNull(),
				'description' => $this->string()->null(),
			],
			$this->color(),
			$this->timestamps(),
			$this->softDelete()
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%task_tag}}';
		$this->dropTable($tableName);
	}
}
