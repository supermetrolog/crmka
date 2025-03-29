<?php

declare(strict_types=1);

namespace app\usecases\Request\Relations;

use app\models\miniModels\RequestObjectType;

class RequestObjectTypeRelationService extends AbstractRequestRelationService
{
	protected string $relationClass     = RequestObjectType::class;
	protected string $relationAttribute = 'object_type';
	protected string $relationGetter    = 'objectTypes';
}
