<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%survey_draft}}`.
 */
class m250522_075932_create_survey_draft_table extends Migration
{
	/**
	 * {@inheritdoc}
	 * @throws Exception
	 */
	public function safeUp()
	{
		$tableName = '{{%survey_draft}}';

		$this->table($tableName, [
			'id'             => $this->primaryKey(),
			'user_id'        => $this->integer()->notNull(),
			'chat_member_id' => $this->integer()->notNull(),
			'data'           => $this->json()->notNull(),
		], $this->timestamps());

		$this->index($tableName, ['user_id']);
		$this->index($tableName, ['chat_member_id']);

		$this->foreignKey($tableName, ['user_id'], 'user', ['id']);
		$this->foreignKey($tableName, ['chat_member_id'], 'chat_member', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%survey_draft}}';

		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->foreignKeyDrop($tableName, ['chat_member_id']);

		$this->dropTable($tableName);
	}
}
