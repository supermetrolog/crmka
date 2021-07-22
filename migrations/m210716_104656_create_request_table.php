<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%request}}`.
 */
class m210716_104656_create_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull()->comment('[связь] ID компании'),
            'dealType' => $this->integer(2)->notNull()->comment('Тип сделки'),
            'expressRequest' => $this->integer()->defaultValue(0)->comment('[флаг] Срочный запрос'),
            'distanceFromMKAD' => $this->integer()->comment('Удаленность от МКАД'),
            'distanceFromMKADnotApplicable' => $this->integer(2)->defaultValue(0)->comment('[флаг] Неприменимо'),
            'minArea' => $this->integer()->notNull()->comment('Минимальная площадь пола'),
            'maxArea' => $this->integer()->notNull()->comment('Максимальная площадь пола'),
            'minCeilingHeight' => $this->integer()->notNull()->comment('Минимальная высота потолков'),
            'maxCeilingHeight' => $this->integer()->notNull()->comment('максимальная высота потолков'),
            'firstFloorOnly' => $this->integer()->defaultValue(0)->comment('[флаг] Только 1 этаж'),
            'heated' => $this->integer()->notNull()->comment('[флаг] Отапливаемый'),
            'antiDustOnly' => $this->integer()->notNull()->comment('[флаг] Только антипыль'),
            'trainLine' => $this->integer()->comment('[флаг] Ж/Д ветка'),
            'trainLineLength' => $this->integer()->comment('Длина Ж/Д'),
            'consultant_id' => $this->integer()->notNull()->comment('[связь] ID консультанта'),
            'description' => $this->string()->comment('Описание'),
            'pricePerFloor' => $this->integer()->comment('Цена за пол'),
            'electricity' => $this->integer()->comment('Электричество'),
            'haveCranes' => $this->integer(2)->comment('[флаг] Наличие кранов'),
            'status' => $this->integer(2)->defaultValue(1)->comment('[флаг] Статус'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex(
            'idx-request-company_id',
            'request',
            'company_id'
        );

        $this->addForeignKey(
            'fk-request-company_id',
            'request',
            'company_id',
            'company',
            'id',
        );
        $this->createIndex(
            'idx-request-consultant_id',
            'request',
            'consultant_id'
        );

        $this->addForeignKey(
            'fk-request-consultant_id',
            'request',
            'consultant_id',
            'user',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%request}}');
    }
}
