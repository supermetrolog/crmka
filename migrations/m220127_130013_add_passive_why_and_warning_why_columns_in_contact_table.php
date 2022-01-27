<?php

use yii\db\Migration;

/**
 * Class m220127_130013_add_passive_why_and_warning_why_columns_in_contact_table
 */
class m220127_130013_add_passive_why_and_warning_why_columns_in_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contact', 'passive_why', $this->tinyInteger()->defaultValue(null)->comment('Причина пассива контакта'));
        $this->addColumn('contact', 'passive_why_comment', $this->string()->defaultValue(null)->comment('Комментарий почему контакт пассив'));
        $this->addColumn('contact', 'warning_why_comment', $this->string()->defaultValue(null)->comment('Комментарий почему есть пометка ВНИМАНИЕ'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220127_130013_add_passive_why_and_warning_why_columns_in_contact_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220127_130013_add_passive_why_and_warning_why_columns_in_contact_table cannot be reverted.\n";

        return false;
    }
    */
}
