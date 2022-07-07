<?php

use yii\db\Migration;

/**
 * Class m220707_130518_alter_notification_body_column
 */
class m220707_130518_alter_notification_body_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('notification', 'body', $this->text()->comment('[html] текст сообщения'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220707_130518_alter_notification_body_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_130518_alter_notification_body_column cannot be reverted.\n";

        return false;
    }
    */
}
