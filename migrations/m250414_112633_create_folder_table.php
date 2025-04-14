<?php

use app\kernel\console\Migration;

class m250414_112633_create_folder_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%folder}}';

		$this->table($tableName, [
			'id'         => $this->primaryKey(),
			'user_id'    => $this->integer()->notNull(),
			'name'       => $this->string(64)->notNull(),
			'icon'       => $this->string(64)->null(),
			'color'      => $this->string(6)->null(),
			'sort_order' => $this->tinyInteger()->notNull()->defaultValue(1),
			'morph'      => $this->string()->notNull()
		], $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['user_id', 'sort_order']);
		$this->index($tableName, ['morph']);


		$this->foreignKey(
			$tableName,
			['user_id'],
			'user',
			['id']
		);
	}

	public function safeDown()
	{
		$tableName = '{{%folder}}';

		$this->foreignKeyDrop($tableName, ['user_id']);

		$this->dropTable($tableName);
	}
}
