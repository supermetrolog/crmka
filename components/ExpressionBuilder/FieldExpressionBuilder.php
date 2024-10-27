<?php

namespace app\components\ExpressionBuilder;

use app\helpers\ArrayHelper;
use app\helpers\NumberHelper;
use app\helpers\StringHelper;
use InvalidArgumentException;
use yii\base\InvalidValueException;

class FieldExpressionBuilder extends ExpressionBuilder
{
	private ?string $field;

	/** @var array<string|int> */
	private array   $values = [];
	private ?string $alias  = null;

	public function field(string $field): self
	{
		if (StringHelper::isEmpty($field)) {
			throw new InvalidValueException('Field expression cannot be empty string');
		}

		$this->field = $field;

		return $this;
	}

	/**
	 * @param string|int $value
	 */
	public function value($value): self
	{
		if (!StringHelper::isString($value) && !NumberHelper::isNumber($value)) {
			throw new InvalidValueException('Value expression must be string or number');
		}

		if (StringHelper::isString($value) && StringHelper::isEmpty($value)) {
			throw new InvalidValueException('Value expression cannot be empty string');
		}

		$this->values[] = $value;

		return $this;
	}

	/**
	 * @param string|int ...$values
	 *
	 * @return self
	 */
	public function values(...$values): self
	{
		if (ArrayHelper::empty($values)) {
			throw new InvalidArgumentException('Field values must be set');
		}

		foreach ($values as $value) {
			$this->value($value);
		}

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
		if (StringHelper::isEmpty($alias)) {
			throw new InvalidValueException('Alias for final expression cannot be empty string');
		}

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
		if (is_null($this->field)) {
			throw new InvalidArgumentException('Field must be set');
		}

		if (ArrayHelper::empty($this->values)) {
			throw new InvalidArgumentException('Field values must be set');
		}
	}

	public function __toString(): string
	{
		$this->validateOrThrow();

		$field      = StringHelper::join(StringHelper::SYMBOL_COMMA, $this->field, ...$this->values);
		$expression = "FIELD($field)";

		$string = ($this->transformFn)($expression);

		if (!is_null($this->alias)) {
			$string .= " AS $this->alias";
		}

		return $string;
	}
}