<?php

declare(strict_types=1);

namespace app\usecases\Request\Relations;

use app\models\miniModels\RequestDistrict;

class RequestDistrictRelationService extends AbstractRequestRelationService
{
	protected string $relationClass     = RequestDistrict::class;
	protected string $relationAttribute = 'district';
	protected string $relationGetter    = 'districts';
}
