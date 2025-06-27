<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%user_tour_status}}`.
 */
class m250626_175221_create_user_tour_status_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%user_tour_status}}';

		$this->table($tableName, [
			'id'       => $this->primaryKey(),
			'user_id'  => $this->integer()->notNull(),
			'tour_id'  => $this->string(64)->notNull(),
			'viewed'   => $this->boolean()->notNull()->defaultValue(true),
			'reset_at' => $this->timestamp()->null()
		], $this->timestamps());

		$this->index($tableName, ['user_id', 'tour_id']);
		$this->index($tableName, ['tour_id']);

		$this->foreignKey($tableName, ['user_id'], 'user', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%user_tour_status}}';

		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->dropTable($tableName);
	}
}
