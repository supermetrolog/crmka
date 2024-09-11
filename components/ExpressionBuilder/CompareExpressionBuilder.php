<?php

namespace app\components\ExpressionBuilder;

use InvalidArgumentException;
use yii\db\Expression;

/**
 * Построитель выражения сравнений для SQL запросов с использованием `yii\db\Expression`
 *
 * Пример использования:
 *
 * ```php
 * $expression = CompareExpressionBuilder::create()
 *         ->left('object.requests')
 *         ->lower()
 *         ->right('object.offers')
 *         ->build();
 *  // equals to:
 *  // $expression = new Expression("object.requests < object.offers");
 *
 * $expression = CompareExpressionBuilder::create()
 *         ->left('object.count')
 *         ->greaterOrEqual(20)
 *  // equals to:
 *  // $expression = new Expression("object.count >= 20");
 * ```
 *
 * @package app\components\ExpressionBuilder
 */
class CompareExpressionBuilder extends ExpressionBuilder
{
	private $operator;
	private $leftExpression;
	private $rightExpression;

	public function __construct($operator = '=', $leftExpression = null, $rightExpression = null)
	{
		$this->operator        = $operator;
		$this->leftExpression  = $leftExpression;
		$this->rightExpression = $rightExpression;
		parent::__construct();
	}

	/**
	 * Устанавливает оператор сравнения
	 *
	 * @param $operator
	 *
	 * @return $this
	 */
	public function operator($operator): self
	{
		$this->operator = $operator;

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "<" (меньше)
	 *
	 * @return $this
	 */
	public function lower($rightExpression = null): self
	{
		$this->operator = '<';

		if (!is_null($rightExpression)) {
			$this->rightExpression = $rightExpression;
		}

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения ">" (больше)
	 *
	 * @return $this
	 */
	public function greater($rightExpression = null): self
	{
		$this->operator = '>';

		if (!is_null($rightExpression)) {
			$this->rightExpression = $rightExpression;
		}

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения ">=" (больше или равно)
	 *
	 * @return $this
	 */
	public function greaterOrEqual(): self
	{
		$this->operator = '>=';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "<=" (меньше или равно)
	 *
	 * @return $this
	 */
	public function lowerOrEqual(): self
	{
		$this->operator = '<=';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "=" (равно)
	 *
	 * @return $this
	 */
	public function equal(): self
	{
		$this->operator = '=';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "!=" (не равно)
	 *
	 * @return $this
	 */
	public function notEqual(): self
	{
		$this->operator = '!=';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "LIKE"
	 *
	 * @return $this
	 */
	public function like(): self
	{
		$this->operator = 'LIKE';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "NOT LIKE"
	 *
	 * @return $this
	 */
	public function notLike(): self
	{
		$this->operator = 'NOT LIKE';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "IN"
	 *
	 * @return $this
	 */
	public function in(): self
	{
		$this->operator = 'IN';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "NOT IN"
	 *
	 * @return $this
	 */
	public function notIn(): self
	{
		$this->operator = 'NOT IN';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "BETWEEN"
	 *
	 * @return $this
	 */
	public function between(): self
	{
		$this->operator = 'BETWEEN';

		return $this;
	}

	/**
	 * Устанавливает оператор сравнения "NOT BETWEEN"
	 *
	 * @return $this
	 */
	public function notBetween(): self
	{
		$this->operator = 'NOT BETWEEN';

		return $this;
	}

	/**
	 * Устанавливает левое выражение
	 *
	 * @param $leftExpression
	 *
	 * @return $this
	 */
	public function left($leftExpression): self
	{
		$this->leftExpression = $leftExpression;

		return $this;
	}

	/**
	 * Устанавливает правое выражение
	 *
	 * @param $rightExpression
	 *
	 * @return $this
	 */
	public function right($rightExpression): self
	{
		$this->rightExpression = $rightExpression;

		return $this;
	}

	/**
	 * Валидирует выражение и выбрасывает исключение, если не все поля установлены
	 *
	 * @return void
	 */
	private function validateOrThrow(): void
	{
		if (is_null($this->leftExpression)) {
			throw new InvalidArgumentException('Left expression must be set');
		}
	}

	public function getCleanExpression(): Expression
	{
		$this->validateOrThrow();

		return new Expression("$this->leftExpression $this->operator $this->rightExpression");
	}

	/**
	 * Возвращает строковое представление выражения
	 *
	 * @return string
	 */
	public function toString(): string
	{
		$this->validateOrThrow();
		$expression = "$this->leftExpression $this->operator $this->rightExpression";

		return ($this->transformFn)($expression);
	}
}