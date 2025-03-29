<?php

declare(strict_types=1);

namespace app\usecases\Request\Relations;

use app\models\miniModels\RequestObjectClass;

class RequestObjectClassRelationService extends AbstractRequestRelationService
{
	protected string $relationClass     = RequestObjectClass::class;
	protected string $relationAttribute = 'object_class';
	protected string $relationGetter    = 'objectClasses';
}
