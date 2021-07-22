<?php

use yii\db\Migration;

/**
 * Class m210702_094650_drop_phone_and_email_and_comment_column
 */
class m210702_094650_drop_phone_and_email_and_comment_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('contact', 'phone');
        $this->dropColumn('contact', 'email');
        $this->dropColumn('contact', 'comment');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210702_094650_drop_phone_and_email_and_comment_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210702_094650_drop_phone_and_email_and_comment_column cannot be reverted.\n";

        return false;
    }
    */
}
