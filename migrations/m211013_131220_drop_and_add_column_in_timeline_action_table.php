<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%and_add_column_in_timeline_action}}`.
 */
class m211013_131220_drop_and_add_column_in_timeline_action_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('timeline_action_comment', 'action');
        $this->addColumn('timeline_action_comment', 'title', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
