<?php

use app\kernel\console\Migration;

class m250819_162349_create_contact_position_table extends Migration
{
	public function safeUp()
	{
		$tableName = '{{%contact_position}}';

		$this->table($tableName,
			[
				'id'            => $this->primaryKey(),
				'created_by_id' => $this->integer()->null(),
				'slug'          => $this->string(64)->null(),
				'name'          => $this->string(64)->notNull(),
				'short_name'    => $this->string(32)->null(),
				'description'   => $this->string(128)->null(),
				'icon'          => $this->string(64)->null(),
				'sort_order'    => $this->integer()->notNull()->defaultValue(100),
				'is_active'     => $this->boolean()->notNull()->defaultValue(true),
			],
			$this->timestamps(),
			$this->softDelete(),
			$this->color()
		);

		$this->foreignKey($tableName, ['created_by_id'], '{{%user}}', ['id']);

		$this->renameColumn('{{%contact}}', 'position', 'position_id');
	}

	public function safeDown()
	{
		$tableName = '{{%contact_position}}';

		$this->foreignKeyDrop($tableName, ['created_by_id']);

		$this->renameColumn('{{%contact}}', 'position_id', 'position');

		$this->dropTable($tableName);
	}
}
