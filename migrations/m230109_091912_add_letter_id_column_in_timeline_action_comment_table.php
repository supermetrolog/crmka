<?php

use yii\db\Migration;

/**
 * Class m230109_091912_add_letter_id_column_in_timeline_action_comment_table
 */
class m230109_091912_add_letter_id_column_in_timeline_action_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("timeline_action_comment", "letter_id", $this->integer()->defaultValue(null)->comment("[СВЯЗЬ] с таблицей писем (letter)"));
        $this->createIndex(
            "idx-timeline_action_comment-letter_id",
            "timeline_action_comment",
            "letter_id",
        );

        $this->addForeignKey(
            "fk-timeline_action_comment-letter_id",
            "timeline_action_comment",
            "letter_id",
            "letter",
            "id",
            "CASCADE"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230109_091912_add_letter_id_column_in_timeline_action_comment_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230109_091912_add_letter_id_column_in_timeline_action_comment_table cannot be reverted.\n";

        return false;
    }
    */
}
