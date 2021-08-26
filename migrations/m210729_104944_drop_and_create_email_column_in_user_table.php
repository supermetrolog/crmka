<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%and_create_email_column_in_user}}`.
 */
class m210729_104944_drop_and_create_email_column_in_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('user', 'email');
        $this->addColumn('user', 'email', $this->string()->unique()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
