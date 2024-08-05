<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%chat_member_message_tag}}`.
 */
class m240504_113909_create_chat_member_message_tag_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%chat_member_message_tag}}';
		$this->table(
			$tableName,
			[
				'id'   => $this->primaryKey(),
				'name' => $this->string()->notNull()
			],
			$this->timestamps(),
			$this->softDelete(),
			$this->morphCol('chat_member_message_tag')
		);

		$tags = [
			'аренда',
			'продажа',
			'развитие',
			'арендаторы',
			'ответ-хранение',
			'субаренда',
		];

		foreach ($tags as $tag) {
			$this->insert($tableName, [
				'name' => $tag
			]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%chat_member_message_tag}}';
		$this->dropTable($tableName);
	}
}
