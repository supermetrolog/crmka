<?php

use app\models\OfferMix;
use app\models\oldDb\ObjectsBlock;
use yii\db\Migration;

/**
 * Class m230527_125838_add_ad_avito_columns_in_offers_mix_table
 */
class m230527_125838_add_ad_avito_columns_in_offers_mix_table extends Migration
{
    public function init()
    {
        parent::init();
        $this->db = Yii::$app->db_old;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = ObjectsBlock::tableName();

        $sql = <<< EOF
            SELECT * FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = '$tableName' AND COLUMN_NAME = :column_name
        EOF;

        $exists = $this->db
            ->createCommand($sql)
            ->bindValue('column_name', 'ad_avito')
            ->queryOne();

        if ($exists) {
            return;
        }

        $this->addColumn(
            ObjectsBlock::tableName(),
            'ad_avito',
            $this->tinyInteger()->notNull()->defaultValue(0),
        );

        $exists = $this->db
            ->createCommand($sql)
            ->bindValue('column_name', 'ad_avito_date_start')
            ->queryOne();

        if ($exists) {
            return;
        }

        $this->addColumn(
            ObjectsBlock::tableName(),
            'ad_avito_date_start',
            $this->timestamp()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(ObjectsBlock::tableName(), 'ad_avito_date_start');
        $this->dropColumn(ObjectsBlock::tableName(), 'ad_avito');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230527_125838_add_ad_avito_columns_in_offers_mix_table cannot be reverted.\n";

        return false;
    }
    */
}
