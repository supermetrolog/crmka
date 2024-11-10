<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%effect}}`.
 */
class m241110_133845_create_effect_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%effect}}';

		$this->table($tableName, [
			'id'          => $this->primaryKey(),
			'title'       => $this->string(64)->notNull(),
			'kind'        => $this->string(64)->notNull()->unique(),
			'description' => $this->string(255)->null(),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%effect}}';

		$this->dropTable($tableName);
	}
}
