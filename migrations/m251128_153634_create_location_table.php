<?php

use app\kernel\console\Migration;

class m251128_153634_create_location_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%location}}';
		$this->table($tableName, [
			'id'                => $this->primaryKey(),
			'kind'              => $this->string(64)->notNull(),
			'name'              => $this->string(64)->null(),
			'is_administrative' => $this->boolean()->notNull()->defaultValue(false),
		], $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['kind']);
	}

	public function safeDown()
	{
		$tableName = '{{%location}}';

		$this->dropTable($tableName);
	}
}
