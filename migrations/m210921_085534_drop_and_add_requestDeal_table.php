<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%and_add_requestDeal}}`.
 */
class m210921_085534_drop_and_add_requestDeal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%request_deal}}');
        $this->createTable('{{%request_deal}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull()->comment('[связб] с запросом'),
            'consultant_id' => $this->integer()->notNull()->comment('[связь] с юзером'),
            'area' => $this->integer()->comment('площадь сделки'),
            'floorPrice' => $this->integer()->comment('цена пола'),
            'clientLegalEntity' => $this->string()->comment('юридическое лицо клиента в сделке'),
            'description' => $this->string()->comment('описание сделки'),
            'startEventTime' => $this->timestamp()->comment('время начала события'),
            'endEventTime' => $this->timestamp()->comment('время завершения события'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex(
            'idx-deal-request_id',
            'request_deal',
            'request_id'
        );

        $this->addForeignKey(
            'fk-request_deal-request_id',
            'request_deal',
            'request_id',
            'request',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-request_deal-consultant_id',
            'request_deal',
            'consultant_id'
        );

        $this->addForeignKey(
            'fk-request_deal-consultant_id',
            'request_deal',
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
        $this->createTable('{{%and_add_requestDeal}}', [
            'id' => $this->primaryKey(),
        ]);
    }
}
