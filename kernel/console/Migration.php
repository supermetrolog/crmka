<?php

declare(strict_types=1);

namespace app\kernel\console;

class Migration extends \yii\db\Migration
{

	public const CURRENT_TIMESTAMP = 'CURRENT_TIMESTAMP';
	public const CASCADE           = 'CASCADE';

	public function timestamps(): array
	{
		return [
			'created_at' => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression(self::CURRENT_TIMESTAMP),
		];
	}

	public function morphCol(string $defaultValue, string $name = 'morph'): array
	{
		return [
			$name => $this->string()->notNull()->defaultValue($defaultValue),
		];
	}

	public function softDelete(): array
	{
		return [
			'deleted_at' => $this->timestamp()->null(),
		];
	}

	public function morph(string $name = 'model', bool $nullable = false): array
	{
		$type = $name . '_type';
		$id   = $name . '_id';

		$idColumn   = $this->bigInteger()->unsigned();
		$typeColumn = $this->string();

		if (!$nullable) {
			$typeColumn->notNull();
			$idColumn->notNull();
		}

		return [
			$type => $typeColumn,
			$id   => $idColumn
		];
	}

	public function addMorphColumn(string $table, string $value, string $name = 'morph'): void
	{
		$this->addColumn($table, $name, $this->string()->notNull()->defaultValue($value));

		$this->index($table, [$name]);
	}

	public function dropMorphColumns(string $table): void
	{
		$this->dropColumn($table, 'morph');
	}

	public function table(string $table, array ...$columns): void
	{
		$this->createTable($table, array_merge(...$columns));
	}

	public function tableWithOption(string $table, string $options, array ...$columns): void
	{
		$this->createTable($table, array_merge(...$columns), $options);
	}

	public function index(string $table, array $columns): void
	{
		$this->createIndex(
			$this->getIndexName($table, $columns),
			$table,
			$columns
		);
	}

	public function indexDrop(string $table, array $columns): void
	{
		$this->dropIndex($this->getIndexName($table, $columns), $table);
	}

	public function unique(string $table, array $columns): void
	{
		$this->createIndex(
			$this->getIndexName($table, $columns),
			$table,
			$columns,
			true
		);
	}


	public function foreignKey(
		string $table,
		array $columns,
		string $refTable,
		array $refColumns,
		?string $delete = null,
		?string $update = null
	): void
	{
		$this->addForeignKey(
			$this->getForeignKeyName($table, $columns),
			$table,
			$columns,
			$refTable,
			$refColumns,
			$delete,
			$update
		);
	}

	public function foreignKeyDrop(string $table, $columns): void
	{
		$this->dropForeignKey($this->getForeignKeyName($table, $columns), $table);
	}

	private function getTableName(string $table): string
	{
		return $this->db->getTableSchema($table)->name;
	}

	private function getIndexName(string $table, array $columns): string
	{
		return implode('-', ['idx', $this->getTableName($table), ...$columns]);
	}

	private function getForeignKeyName(string $table, array $columns): string
	{
		return implode('-', ['fk', $this->getTableName($table), ...$columns]);
	}
}