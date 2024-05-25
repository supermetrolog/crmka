<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%media}}`.
 */
class m240525_090140_create_media_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%media}}';
		$this->table($tableName, [
			'id'            => $this->primaryKey(),
			'name'          => $this->string()->notNull(),
			'original_name' => $this->string()->notNull(),
			'extension'     => $this->string()->notNull(),
			'path'          => $this->string()->notNull(),
			'category'      => $this->string()->notNull(),
		], $this->morph('model'), ['created_at' => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP)], $this->softDelete());


		$this->index(
			$tableName,
			['model_type', 'model_id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%media}}';
		$this->dropTable($tableName);
	}
}
