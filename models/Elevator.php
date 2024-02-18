<?php

declare(strict_types=1);

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use yii\db\ActiveQuery;
use yii\helpers\Json;

class Elevator extends oldDb\Elevator
{

	public function getElevatorControls(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->elevator_controls);
	}

	public function getPhotos(): array
	{
		return Json::decode($this->photo) ?? [];
	}

	public function fields()
	{
		$f = parent::fields();

		$f['elevator_controls'] = function () {
			return $this->getElevatorControls();
		};

		$f['photos'] = function () {
			return $this->getPhotos();
		};

		return $f;
	}

	public function getElevatorType(): ActiveQuery
	{
		return $this->hasOne(ElevatorType::class, ['id' => 'elevator_type']);
	}
}