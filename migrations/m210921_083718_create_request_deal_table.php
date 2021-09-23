<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request_deal}}`.
 */
class m210921_083718_create_request_deal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request_deal}}');
    }
}
