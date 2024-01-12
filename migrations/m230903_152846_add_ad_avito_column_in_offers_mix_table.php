<?php

use yii\db\Migration;

/**
 * Class m230903_152846_add_ad_avito_column_in_offers_mix_table
 */
class m230903_152846_add_ad_avito_column_in_offers_mix_table extends Migration
{
    public function init()
    {
        $this->db = Yii::$app->db_old;
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (YII_ENV !== 'staging') {
            $this->addColumn(
                'c_industry_offers_mix',
                'ad_avito',
                $this->tinyInteger()->notNull()->defaultValue(0)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('c_industry_offers_mix', 'ad_avito');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230903_152846_add_ad_avito_column_in_offers_mix_table cannot be reverted.\n";

        return false;
    }
    */
}
