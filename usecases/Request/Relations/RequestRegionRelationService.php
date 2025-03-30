<?php

declare(strict_types=1);

namespace app\usecases\Request\Relations;

use app\models\miniModels\RequestRegion;

class RequestRegionRelationService extends AbstractRequestRelationService
{
	protected string $relationClass     = RequestRegion::class;
	protected string $relationAttribute = 'region';
	protected string $relationGetter    = 'regions';
}
