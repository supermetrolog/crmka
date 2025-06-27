<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%user_tour_view}}`.
 */
class m250626_120324_create_user_tour_view_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%user_tour_view}}';

		$this->table($tableName, [
			'id'           => $this->primaryKey(),
			'user_id'      => $this->integer()->notNull(),
			'tour_id'      => $this->string(64)->notNull(),
			'steps_viewed' => $this->integer(2)->notNull(),
			'steps_total'  => $this->integer(2)->notNull(),
			'created_at'   => $this->timestamp()->defaultExpression(self::CURRENT_TIMESTAMP),
		]);

		$this->index($tableName, ['user_id', 'tour_id']);
		$this->index($tableName, ['tour_id']);

		$this->foreignKey($tableName, ['user_id'], 'user', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%user_tour_view}}';

		$this->foreignKeyDrop($tableName, ['user_id']);
		$this->dropTable($tableName);
	}
}
