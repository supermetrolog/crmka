<?php

use yii\db\Migration;

/**
 * Class m211115_114313_update_type_form_of_organization_field
 */
class m211115_114313_update_type_form_of_organization_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('company', 'formOfOrganization');
        $this->addColumn('company', 'formOfOrganization', $this->tinyInteger(1)->defaultValue(null)->comment('Форма организации - ООО, ОАО ...'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211115_114313_update_type_form_of_organization_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211115_114313_update_type_form_of_organization_field cannot be reverted.\n";

        return false;
    }
    */
}
