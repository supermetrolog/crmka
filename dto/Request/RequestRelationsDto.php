<?php

declare(strict_types=1);

namespace app\dto\Request;

use yii\base\BaseObject;

class RequestRelationsDto extends BaseObject
{
	/** @var int[] */
	public array $direction_ids = [];

	/** @var int[] */
	public array $district_ids = [];

	/** @var int[] */
	public array $gate_types = [];

	/** @var int[] */
	public array $object_classes = [];

	/** @var int[] */
	public array $region_ids = [];

	/** @var int[] */
	public array $object_type_ids = [];

	/** @var int[] */
	public array $object_type_general_ids = [];
}