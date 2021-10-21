<?php

use yii\db\Migration;

/**
 * Class m210930_140212_add_cascade_fk_for_company_ralations_tables
 */
class m210930_140212_add_cascade_fk_for_company_ralations_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->dropForeignKey('fk-productRange-company_id', 'productrange');
        $this->addForeignKey(
            'fk-productRange-company_id',
            'productrange',
            'company_id',
            'company',
            'id',
            'CASCADE'
        );
        $this->dropForeignKey('fk-company-company_id', 'contact');

        $this->addForeignKey(
            'fk-company-company_id',
            'contact',
            'company_id',
            'company',
            'id',
            'CASCADE'
        );

        $this->dropForeignKey('fk-request-company_id', 'request');

        $this->addForeignKey(
            'fk-request-company_id',
            'request',
            'company_id',
            'company',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210930_140212_add_cascade_fk_for_company_ralations_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210930_140212_add_cascade_fk_for_company_ralations_tables cannot be reverted.\n";

        return false;
    }
    */
}
