<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service_selection}}`.
 */
class m220707_115019_create_service_selection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%service_selection}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с запросом'),
            'recommended_offers_count' => $this->integer()->comment('колличество предложений в последей подборке'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(),
        ]);

        $this->createIndex(
            'idx-service_selection-request_id',
            'service_selection',
            'request_id'
        );

        $this->addForeignKey(
            'fk-service_selection-request_id',
            'service_selection',
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
        $this->dropTable('{{%service_selection}}');
    }
}
