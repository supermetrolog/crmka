<?php

use yii\db\Migration;

/**
 * Class m211022_135850_drop_and_add_fk_for_email_phone_website
 */
class m211022_135850_drop_and_add_fk_for_email_phone_website extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-website-contact_id', 'website');

        $this->addForeignKey(
            'fk-website-contact_id',
            'website',
            'contact_id',
            'contact',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211022_135850_drop_and_add_fk_for_email_phone_website cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211022_135850_drop_and_add_fk_for_email_phone_website cannot be reverted.\n";

        return false;
    }
    */
}
