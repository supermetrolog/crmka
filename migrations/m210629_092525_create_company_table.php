<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 */
class m210629_092525_create_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
            'nameEng' => $this->string(255)->defaultValue(null),
            'nameRu' => $this->string(255)->defaultValue(null),
            'noName' => $this->boolean()->defaultValue(0),
            'nameRu' => $this->string(255)->defaultValue(null),
            'formOfOrganization' => $this->string(255)->defaultValue(null),
            'companyGroup_id' => $this->integer(11)->defaultValue(null),
            'officeAdress' => $this->string(255)->defaultValue(null),
            'status' => $this->integer(11)->defaultValue(0),
            'consultant_id' => $this->integer(11)->notNull(),
            'broker_id' => $this->integer(11)->defaultValue(null),
            'legalAddress' => $this->string(255)->defaultValue(null),
            'ogrn' => $this->string(255)->defaultValue(null),
            'inn' => $this->string(255)->defaultValue(null),
            'kpp' => $this->string(255)->defaultValue(null),
            'checkingAccount' => $this->string(255)->defaultValue(null),
            'correspondentAccount' => $this->string(255)->defaultValue(null),
            'inTheBank' => $this->string(255)->defaultValue(null),
            'bik' => $this->string(255)->defaultValue(null),
            'okved' => $this->string(255)->defaultValue(null),
            'okpo' => $this->string(255)->defaultValue(null),
            'signatoryName' => $this->string(255)->defaultValue(null),
            'signatoryMiddleName' => $this->string(255)->defaultValue(null),
            'signatoryLastName' => $this->string(255)->defaultValue(null),
            'basis' => $this->string(255)->defaultValue(null),
            'documentNumber' => $this->string(255)->defaultValue(null),
            'activityGroup' => $this->integer(11)->notNull(),
            'activityProfile' => $this->integer(11)->notNull(),
            'description' => $this->text()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null)
        ]);
        $this->createIndex(
            'idx-company-companyGroup_id',
            'company',
            'companyGroup_id'
        );

        $this->addForeignKey(
            'fk-company-companyGroup_id',
            'company',
            'companyGroup_id',
            'companyGroup',
            'id',
        );
        $this->createIndex(
            'idx-company-consultant_id',
            'company',
            'consultant_id'
        );

        $this->addForeignKey(
            'fk-company-consultant_id',
            'company',
            'consultant_id',
            'user',
            'id',
        );
        $this->createIndex(
            'idx-company-broker_id',
            'company',
            'broker_id'
        );

        $this->addForeignKey(
            'fk-company-broker_id',
            'company',
            'broker_id',
            'user',
            'id',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%company}}');
    }
}
