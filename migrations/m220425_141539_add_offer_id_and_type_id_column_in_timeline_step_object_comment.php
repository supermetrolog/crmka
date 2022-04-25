<?php

use yii\db\Migration;

/**
 * Class m220425_141539_add_offer_id_and_type_id_column_in_timeline_step_object_comment
 */
class m220425_141539_add_offer_id_and_type_id_column_in_timeline_step_object_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-timeline_step_object_comment-object_id', 'timeline_step_object_comment');
        $this->dropIndex('idx-timeline_step_object_comment-object_id', 'timeline_step_object_comment');
        $this->dropColumn('timeline_step_object_comment', 'object_id');

        $this->addColumn('timeline_step_object_comment', 'offer_id', $this->integer()->notNull()->comment('[СВЯЗЬ] с original_id в c_industry_offers_mix'));
        $this->addColumn('timeline_step_object_comment', 'type_id', $this->tinyInteger()->notNull()->comment('[СВЯЗЬ] с type_id в c_industry_offers_mix'));
        $this->addColumn('timeline_step_object_comment', 'object_id', $this->integer()->notNull()->comment('[СВЯЗЬ] с object_id в c_industry_offers_mix'));
        $this->addColumn('timeline_step_object_comment', 'timeline_step_object_id', $this->integer()->notNull()->comment('[СВЯЗЬ] с timeline_step_object'));

        $this->createIndex(
            'idx-timeline_step_object_comment-object_id',
            'timeline_step_object_comment',
            'object_id'
        );
        $this->createIndex(
            'idx-timeline_step_object_comment-offer_id',
            'timeline_step_object_comment',
            'offer_id'
        );
        $this->createIndex(
            'idx-timeline_step_object_comment-type_id',
            'timeline_step_object_comment',
            'type_id'
        );
        $this->createIndex(
            'idx-timeline_step_object_comment-timeline_step_object_id',
            'timeline_step_object_comment',
            'timeline_step_object_id'
        );

        $this->addForeignKey(
            'fk-timeline_step_object_comment-timeline_step_object_id',
            'timeline_step_object_comment',
            'timeline_step_object_id',
            'timeline_step_object',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220425_141539_add_offer_id_and_type_id_column_in_timeline_step_object_comment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220425_141539_add_offer_id_and_type_id_column_in_timeline_step_object_comment cannot be reverted.\n";

        return false;
    }
    */
}
