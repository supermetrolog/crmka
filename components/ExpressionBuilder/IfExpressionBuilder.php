<?php

namespace app\components\ExpressionBuilder;

use InvalidArgumentException;
use yii\db\Expression;

/**
 * Построитель выражения `IF` для SQL запросов с использованием `yii\db\Expression`
 *
 * Пример использования:
 *
 * ```php
 * $expression = IfExpressionBuilder::create()
 *         ->condition('object.count > 10')
 *         ->left(1)
 *         ->right(0)
 *         ->as('is_big')
 *         ->build();
 *  // equals to:
 *  // $expression = new Expression("IF(object.count > 10, 1, 0) AS is_big");
 * ```
 *
 * @package app\components\ExpressionBuilder
 */
class IfExpressionBuilder extends ExpressionBuilder
{
	private ?string $condition;
	private ?string $trueExpression;
	private ?string $falseExpression;
	private ?string $alias = null;

	public function __construct(?string $condition = null, ?string $trueExpression = "1", ?string $falseExpression = "0")
	{
		$this->condition       = $condition;
		$this->trueExpression  = $trueExpression;
		$this->falseExpression = $falseExpression;

		parent::__construct();
	}

	/**
	 * Устанавливает условие для оператора `IF`
	 *
	 * @param string $condition
	 *
	 * @return $this
	 */
	public function condition(string $condition): self
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * Устанавливает выражение для ветки `THEN`
	 *
	 * @param ?string $trueExpression
	 *
	 * @return $this
	 */
	public function left(?string $trueExpression): self
	{
		$this->trueExpression = $trueExpression;

		return $this;
	}

	/**
	 * Устанавливает выражение для ветки `ELSE`
	 *
	 * @param ?string $falseExpression
	 *
	 * @return $this
	 */
	public function right(?string $falseExpression): self
	{
		$this->falseExpression = $falseExpression;

		return $this;
	}

	/**
	 * Устанавливает псевдоним для выражения
	 *
	 * @param string $alias
	 *
	 * @return $this
	 */
	public function as(string $alias): self
	{
		$this->alias = $alias;

		return $this;
	}

	/**
	 * Валидирует выражение и выбрасывает исключение, если не все поля установлены
	 *
	 * @return void
	 */
	private function validateOrThrow(): void
	{
		if (is_null($this->condition)) {
			throw new InvalidArgumentException('Condition must be set');
		}
	}

	/**
	 * Возвращает созданное выражение без примненения `beforeBuild`
	 *
	 * @return Expression
	 */
	public function getCleanExpression(): Expression
	{
		return new Expression("IF($this->condition, $this->trueExpression, $this->falseExpression)");
	}

	public function __toString(): string
	{
		$expression = "IF($this->condition, $this->trueExpression, $this->falseExpression)";
		$string     = ($this->transformFn)($expression);

		if (!is_null($this->alias)) {
			$string .= " AS $this->alias";
		}

		return $string;
	}
}