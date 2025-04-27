<?php

use app\kernel\console\Migration;


/**
 * Handles the creation of table `{{%chat_member}}`.
 */
class m240330_102342_create_chat_member_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		/*

		TASKS

		id, user_id, created_by_type, created_by_id, message, start, end, status


		CHAT MEMBERS

		id, model_type (request, offer, user), model_id

		CHAT_MEMBER_MESSAGE

		id, to_chat_member_id, from_chat_member_id | NULL, message | NULL


		CHAT MEMBER MESSAGE TASKS

		id, task_id, chat_member_message_id

		 */

		$tableName = '{{%chat_member}}';

		$this->table($tableName, [
			'id' => $this->primaryKey(),
		], $this->morphBigInteger(), $this->timestamps());

		$this->unique($tableName, ['model_type', 'model_id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropTable('{{%chat_member}}');
	}
}
