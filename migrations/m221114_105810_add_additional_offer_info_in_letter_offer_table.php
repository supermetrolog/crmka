<?php

use yii\db\Migration;

/**
 * Class m221114_105810_add_additional_offer_info_in_letter_offer_table
 */
class m221114_105810_add_additional_offer_info_in_letter_offer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("letter_offer", "class_name", $this->string()->comment("класс объекта"));
        $this->addColumn("letter_offer", "deal_type_name", $this->string()->comment("тип сделки"));
        $this->addColumn("letter_offer", "visual_id", $this->string()->comment("визуальный ID объекта"));
        $this->addColumn("letter_offer", "address", $this->string()->comment("адрес объекта"));
        $this->addColumn("letter_offer", "area", $this->string()->comment("площадь предложения"));
        $this->addColumn("letter_offer", "price", $this->string()->comment("цена предложения"));
        $this->addColumn("letter_offer", "image", $this->string()->comment("фото"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221114_105810_add_additional_offer_info_in_letter_offer_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221114_105810_add_additional_offer_info_in_letter_offer_table cannot be reverted.\n";

        return false;
    }
    */
}
