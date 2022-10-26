<?php

use yii\db\Migration;

/**
 * Class m221026_124606_change_fk_for_letter_contact_and_letter_offer_tables
 */
class m221026_124606_change_fk_for_letter_contact_and_letter_offer_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-letter_offer-letter_id', 'letter_offer');
        $this->dropForeignKey('fk-letter_contact-letter_id', 'letter_contact');
        $this->dropForeignKey('fk-letter_way-letter_id', 'letter_way');

        $this->addForeignKey(
            'fk-letter_offer-letter_id',
            'letter_offer',
            'letter_id',
            'letter',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-letter_contact-letter_id',
            'letter_contact',
            'letter_id',
            'letter',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-letter_way-letter_id',
            'letter_way',
            'letter_id',
            'letter',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221026_124606_change_fk_for_letter_contact_and_letter_offer_tables cannot be reverted.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221026_124606_change_fk_for_letter_contact_and_letter_offer_tables cannot be reverted.\n";

        return false;
    }
    */
}
