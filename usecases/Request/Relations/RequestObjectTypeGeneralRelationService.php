<?php

declare(strict_types=1);

namespace app\usecases\Request\Relations;

use app\models\miniModels\RequestObjectTypeGeneral;

class RequestObjectTypeGeneralRelationService extends AbstractRequestRelationService
{
	protected string $relationClass     = RequestObjectTypeGeneral::class;
	protected string $relationAttribute = 'type';
	protected string $relationGetter    = 'objectTypesGeneral';
}
