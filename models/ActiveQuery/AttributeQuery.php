<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\ModelAQ;
use app\models\Attribute;

/**
 * @extends ModelAQ<Attribute>
 */
class AttributeQuery extends ModelAQ
{
	public function byLabel(string $label): self
	{
		return $this->andWhere([$this->field('label') => $label]);
	}

	public function byKind(string $kind): self
	{
		return $this->andWhere([$this->field('kind') => $kind]);
	}
}