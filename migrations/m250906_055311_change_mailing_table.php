<?php

use app\kernel\console\Migration;

class m250906_055311_change_mailing_table extends Migration
{
	public function safeUp(): void
	{
		$tableName = '{{%mailing}}';

		$this->alterColumn($tableName, 'created_by_id', $this->bigInteger()->unsigned()->null());
		$this->alterColumn($tableName, 'created_by_type', $this->string(255)->null());
	}

	public function safeDown(): void
	{
		$tableName = '{{%mailing}}';

		$this->alterColumn($tableName, 'created_by_id', $this->bigInteger()->unsigned()->notNull());
		$this->alterColumn($tableName, 'created_by_type', $this->string(255)->notNull());
	}
}