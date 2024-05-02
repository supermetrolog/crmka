<?php

declare(strict_types=1);

namespace app\kernel\common\models\AQ;

/**
 * @mixin AQ
 */
trait SoftDeleteTrait
{
	public function notDeleted(): self
	{
		return $this->andWhereNull($this->field($this->modelClass::SOFT_DELETE_ATTRIBUTE));
	}

	public function deleted(): self
	{
		return $this->andWhereNotNull($this->field($this->modelClass::SOFT_DELETE_ATTRIBUTE));
	}
}