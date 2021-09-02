<?php

use yii\db\Migration;

/**
 * Class m210831_115327_add_columns_in_contact_comment_table
 */
class m210831_115327_add_columns_in_contact_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contact_comment', 'created_at', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));

        $this->dropForeignKey('fk-comment-contact_id', 'contact_comment');
        $this->dropForeignKey('fk-author-author_id', 'contact_comment');
        $this->addForeignKey(
            'fk-comment-contact_id',
            'contact_comment',
            'contact_id',
            'contact',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-author-author_id',
            'contact_comment',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210831_115327_add_columns_in_contact_comment_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210831_115327_add_columns_in_contact_comment_table cannot be reverted.\n";

        return false;
    }
    */
}
