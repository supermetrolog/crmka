<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%reminder}}`.
 */
class m240522_221408_create_reminder_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableName = '{{%reminder}}';
		$this->table($tableName, [
			'id'        => $this->primaryKey(),
			'user_id'   => $this->integer()->notNull(),
			'message'   => $this->text()->notNull(),
			'status'    => $this->tinyInteger()->notNull(),
            'notify_at' => $this->timestamp()->notNull(),
		], $this->morph('created_by'), $this->timestamps(), $this->softDelete());

        $this->addMorphColumn($tableName);
        

		$this->index(
			$tableName,
			['user_id']
		);

		$this->index(
			$tableName,
			['created_by_type', 'created_by_id']
		);

		$this->foreignKey(
			$tableName,
			['user_id'],
			'user',
			['id']
		);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$tableName = '{{%reminder}}';
        $this->dropTable($tableName);
    }
}
