<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_object_type_general}}`.
 */
class m220527_122443_create_request_object_type_general_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_object_type_general}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с запросом'),
            'type' => $this->tinyInteger(1)->notNull()->comment('Тип объекта (0 - склад, 1 - производство, 2 - участок')
        ]);

        $this->createIndex('idx-request_object_type_general-request_id', 'request_object_type_general', 'request_id');
        $this->addForeignKey(
            'fk-request_object_type_general-request_id',
            'request_object_type_general',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request_object_type_general}}');
    }
}
