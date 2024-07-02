<?php

use app\kernel\console\Migration;

/**
 * Handles the creation of table `{{%equipment}}`.
 */
class m240701_165350_create_equipment_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableName = '{{%equipment}}';
		$this->table($tableName, [
			'id'              => $this->primaryKey(),
			'name'            => $this->string(60)->notNull(),
			'address'         => $this->string()->notNull(),
			'description'     => $this->text()->notNull(),
			'company_id'      => $this->integer()->notNull(),
			'contact_id'      => $this->integer()->notNull(),
			'consultant_id'   => $this->integer()->notNull(),
			'preview_id'      => $this->integer()->notNull(),
			'category'        => $this->integer()->notNull(),
			'availability'    => $this->integer()->notNull(),
			'delivery'        => $this->integer()->notNull(),
			'deliveryPrice'   => $this->integer()->null(),
			'price'           => $this->integer()->notNull(),
			'benefit'         => $this->integer()->notNull(),
			'tax'             => $this->integer()->notNull(),
			'count'           => $this->integer()->notNull(),
			'state'           => $this->integer()->notNull(),
			'status'          => $this->integer()->notNull(),
			'passive_type'    => $this->integer()->null(),
			'passive_comment' => $this->text()->null(),
			'archived_at'     => $this->timestamp()->null(),
		], $this->morph('created_by'), $this->timestamps(), $this->softDelete());

		$this->index($tableName, ['company_id']);
		$this->index($tableName, ['contact_id']);
		$this->index($tableName, ['consultant_id']);
		$this->index($tableName, ['preview_id']);

		$this->foreignKey($tableName, ['company_id'], 'company', ['id']);
		$this->foreignKey($tableName, ['contact_id'], 'contact', ['id']);
		$this->foreignKey($tableName, ['consultant_id'], 'user', ['id']);
		$this->foreignKey($tableName, ['preview_id'], 'media', ['id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$tableName = '{{%equipment}}';

		$this->foreignKeyDrop($tableName, ['company_id']);
		$this->foreignKeyDrop($tableName, ['contact_id']);
		$this->foreignKeyDrop($tableName, ['consultant_id']);
		$this->foreignKeyDrop($tableName, ['preview_id']);
		$this->dropTable($tableName);
	}
}

/*

//	array files - список документов-файлов
//	array photos - список документов-фоток


object last_ccall - привязка к последнему звонку

{
  1: 'Стеллажное оборудование'
  2: 'Станки'
  3: 'Крановые устройства'
  4: 'Подъемные устройства'
  5: 'Погрузо-разгрузочная техника'
  6: 'Серверное оборудование'
  7: 'Другое'
}

 */