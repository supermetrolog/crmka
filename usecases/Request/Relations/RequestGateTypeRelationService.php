<?php

declare(strict_types=1);

namespace app\usecases\Request\Relations;

use app\models\miniModels\RequestGateType;

class RequestGateTypeRelationService extends AbstractRequestRelationService
{
	protected string $relationClass     = RequestGateType::class;
	protected string $relationAttribute = 'gate_type';
	protected string $relationGetter    = 'gateTypes';
}
