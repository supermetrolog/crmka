<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%and_add_comment_column_in_timeline_action_comment}}`.
 */
class m211021_125411_drop_and_add_comment_column_in_timeline_action_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('timeline_action_comment', 'comment');
        $this->addColumn('timeline_action_comment', 'comment', $this->text()->notNull()->comment('комментарий к действию'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
