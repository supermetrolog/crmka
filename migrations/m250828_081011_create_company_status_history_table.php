<?php

use app\kernel\console\Migration;

class m250828_081011_create_company_status_history_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%company_status_history}}';

		$this->table($tableName, [
			'id'                => $this->primaryKey(),
			'company_id'        => $this->integer()->notNull(),
			'status'            => $this->tinyInteger(2)->notNull(),
			'reason'            => $this->string(32)->null(),
			'comment'           => $this->string(255)->null(),
			'changed_by_id'     => $this->integer()->null(),
			'changed_by_source' => $this->string(32)->notNull(),
			'created_at'        => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP),
		]);

		$this->foreignKey($tableName, ['company_id'], 'company', ['id']);
		$this->foreignKey($tableName, ['changed_by_id'], 'user', ['id']);

		$this->index($tableName, ['company_id']);
		$this->index($tableName, ['changed_by_id', 'changed_by_source']);

		$this->index('{{%company}}', ['status']);
	}

	public function safeDown()
	{
		$tableName = '{{%company_status_history}}';

		$this->foreignKeyDrop($tableName, ['company_id']);
		$this->foreignKeyDrop($tableName, ['changed_by_id']);

		$this->indexDrop('{{%company}}', ['status']);

		$this->dropTable($tableName);
	}
}
