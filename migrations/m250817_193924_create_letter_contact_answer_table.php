<?php

use app\kernel\console\Migration;

class m250817_193924_create_letter_contact_answer_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%letter_contact_answer}}';

		$this->table($tableName, [
			'id'                 => $this->primaryKey(),
			'letter_contact_id'  => $this->integer()->notNull(),
			'marked_by_id'       => $this->integer()->notNull(),
			'marked_at'          => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP),
			'type'               => $this->string(16)->notNull(),
			'comment'            => $this->text()->null(),
			'related_message_id' => $this->string()->null()
		], $this->softDelete());

		$this->index($tableName, ['marked_by_id']);
		$this->index($tableName, ['letter_contact_id']);
		$this->index($tableName, ['type']);

		$this->foreignKey($tableName, ['letter_contact_id'], 'letter_contact', ['id']);
		$this->foreignKey($tableName, ['marked_by_id'], 'user', ['id']);
	}

	public function safeDown()
	{
		$tableName = '{{%letter_contact_answer}}';

		$this->foreignKeyDrop($tableName, ['letter_contact_id']);
		$this->foreignKeyDrop($tableName, ['marked_by_id']);

		$this->dropTable($tableName);
	}
}
