<?php

declare(strict_types=1);

namespace app\dto\Request;

use DateTimeInterface;
use yii\base\BaseObject;

abstract class AbstractRequestDto extends BaseObject
{
	public int  $contact_id;
	public int  $consultant_id;
	public int  $dealType;
	public int  $minArea;
	public int  $maxArea;
	public int  $minCeilingHeight;
	public ?int $maxCeilingHeight;
	public ?int $distanceFromMKAD;
	public ?int $pricePerFloor;
	public ?int $trainLineLength;
	public ?int $electricity;
	public ?int $unknownMovingDate;

	public ?bool $outside_mkad;
	public ?bool $region_neardy;
	public ?bool $distanceFromMKADnotApplicable;
	public ?bool $firstFloorOnly;
	public ?bool $expressRequest;
	public ?bool $heated;
	public ?bool $antiDustOnly;
	public ?bool $trainLine;
	public ?bool $haveCranes;
	public ?bool $water;
	public ?bool $sewerage;
	public ?bool $gaz;
	public ?bool $steam;
	public ?bool $shelving;

	public ?string $name;
	public ?string $description;

	public ?DateTimeInterface $movingDate;

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