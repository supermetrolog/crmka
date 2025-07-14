<?php

use app\kernel\console\Migration;

/**
 * Class m250709_121427_change_phone_table
 */
class m250709_121427_change_phone_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%phone}}';

		$this->addColumns($tableName, [
			'country_code' => $this->string(3)->notNull()->defaultValue('RU'),
			'type'         => $this->string(16)->notNull()->defaultValue('mobile'),
			'comment'      => $this->string(128)->null(),
			'status'       => $this->string(16)->notNull()->defaultValue('active'),
		], $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['status']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%phone}}';

		$this->dropColumns($tableName, ['country_code', 'type', 'comment', 'status', 'deleted_at', 'created_at', 'updated_at']);
	}
}