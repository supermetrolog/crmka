<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%index_for_relation}}`.
 */
class m240726_192906_create_index_for_relation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableName = '{{%relation}}';

	    $this->index($tableName, ['first_id', 'first_type']);
	    $this->index($tableName, ['second_id', 'second_type']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
	    $tableName = '{{%relation}}';

	    $this->indexDrop($tableName, ['first_id', 'first_type']);
	    $this->indexDrop($tableName, ['second_id', 'second_type']);
    }
}
