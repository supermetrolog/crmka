<?php

use yii\db\Migration;

/**
 * Class m210702_103539_drop_fk_and_add_fk_for_phone_email_contact_comment
 */
class m210702_103539_drop_fk_and_add_fk_for_phone_email_contact_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-contact-contact_id', 'email');
        $this->dropForeignKey('fk-phone-contact_id', 'phone');
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
        $this->addForeignKey(
            'fk-phone-contact_id',
            'phone',
            'contact_id',
            'contact',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-contact-contact_id',
            'email',
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
        echo "m210702_103539_drop_fk_and_add_fk_for_phone_email_contact_comment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210702_103539_drop_fk_and_add_fk_for_phone_email_contact_comment cannot be reverted.\n";

        return false;
    }
    */
}
