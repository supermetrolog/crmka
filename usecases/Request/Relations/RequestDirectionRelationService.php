<?php

declare(strict_types=1);

namespace app\usecases\Request\Relations;

use app\models\miniModels\RequestDirection;

class RequestDirectionRelationService extends AbstractRequestRelationService
{
	protected string $relationClass     = RequestDirection::class;
	protected string $relationAttribute = 'direction';
	protected string $relationGetter    = 'directions';
}
