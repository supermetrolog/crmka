<?php

use app\kernel\console\Migration;

class m250818_082411_create_survey_action_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%survey_action}}';

		$this->table($tableName, [
			'id'            => $this->primaryKey(),
			'survey_id'     => $this->integer()->notNull(),
			'type'          => $this->string(32)->notNull(),
			'target_id'     => $this->integer()->null(),
			'status'        => $this->string(16)->notNull(),
			'completed_at'  => $this->timestamp()->null(),
			'created_by_id' => $this->integer()->notNull(),
			'comment'       => $this->text()->null(),
		], $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['survey_id']);
		$this->index($tableName, ['target_id']);
		$this->index($tableName, ['created_by_id']);

		$this->foreignKey($tableName, ['survey_id'], 'survey', ['id']);
		$this->foreignKey($tableName, ['created_by_id'], 'user', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%survey_action}}';

		$this->foreignKeyDrop($tableName, ['survey_id']);
		$this->foreignKeyDrop($tableName, ['created_by_id']);

		$this->dropTable($tableName);
	}
}
