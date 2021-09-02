<?php

use yii\db\Migration;

/**
 * Class m210831_091732_add_new_column_in_contact_table
 */
class m210831_091732_add_new_column_in_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contact', 'consultant_id', $this->integer()->comment('[связь] с пользователями'));
        $this->addColumn('contact', 'position', $this->integer()->comment('Должность'));
        $this->addColumn('contact', 'faceToFaceMeeting', $this->boolean()->comment('[флаг] Очная встреча'));
        $this->addColumn('contact', 'warning', $this->boolean()->comment('[флаг] Внимание'));
        $this->addColumn('contact', 'good', $this->boolean()->comment('[флаг] Хор. взаимоотношения'));
        $this->createIndex(
            'idx-contact-consultant_id',
            'contact',
            'consultant_id'
        );

        $this->addForeignKey(
            'fk-contact-consultant_id',
            'contact',
            'consultant_id',
            'user',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210831_091732_add_new_column_in_contact_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210831_091732_add_new_column_in_contact_table cannot be reverted.\n";

        return false;
    }
    */
}
