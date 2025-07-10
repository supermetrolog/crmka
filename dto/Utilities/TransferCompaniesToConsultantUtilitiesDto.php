<?php

declare(strict_types=1);

namespace app\dto\Utilities;

use yii\base\BaseObject;

class TransferCompaniesToConsultantUtilitiesDto extends BaseObject
{
	/** @var int[] */
	public array $companyIds;
}