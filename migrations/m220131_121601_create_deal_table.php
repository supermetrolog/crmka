<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%deal}}`.
 */
class m220131_121601_create_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%deal}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с компанией'),
            'request_id' => $this->integer()->defaultValue(null)->comment('[СВЯЗЬ] с запросом'),
            'consultant_id' => $this->integer()->notNull()->comment('[СВЯЗЬ] с юзером'),
            'area' => $this->integer()->defaultValue(null)->comment('площадь сделки'),
            'floorPrice' => $this->integer()->defaultValue(null)->comment('цена пола'),
            'clientLegalEntity' => $this->string()->defaultValue(null)->comment('юр. лицо клиента в сделке'),
            'description' => $this->string()->defaultValue(null)->comment('описание'),
            'startEventTime' => $this->timestamp()->defaultValue(null)->comment('врменя начала события'),
            'endEventTime' => $this->timestamp()->defaultValue(null)->comment('врменя конца события'),
            'name' => $this->string()->defaultValue(null)->comment('название сделки'),
            'object_id' => $this->integer()->defaultValue(null)->comment('ID объекта из старой базы'),
            'complex_id' => $this->integer()->defaultValue(null)->comment('ID комплекса из старой базы'),
        ]);
        $this->createIndex(
            'idx-deal-request_id',
            'deal',
            'request_id'
        );

        $this->addForeignKey(
            'fk-deal-request_id',
            'deal',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-deal-consultant_id',
            'deal',
            'consultant_id'
        );

        $this->addForeignKey(
            'fk-deal-consultant_id',
            'deal',
            'consultant_id',
            'user',
            'id'
        );
        $this->createIndex(
            'idx-deal-company_id',
            'deal',
            'company_id'
        );

        $this->addForeignKey(
            'fk-deal-company_id',
            'deal',
            'company_id',
            'company',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%deal}}');
    }
}
