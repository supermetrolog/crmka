<?php

declare(strict_types=1);

namespace app\models\oldDb;

use app\exceptions\ValidationErrorHttpException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class OfferMixMapSearch extends OfferMixSearch
{
	/**
	 * @return ActiveQuery
	 */
	private function getQuery(): ActiveQuery
	{
		$select = [
			$this->getField('latitude'),
			$this->getField('longitude'),
			$this->getField('address'),
			$this->getField('complex_id'),
			$this->getField('object_id'),
			$this->getField('type_id'),
			$this->getField('original_id'),
			$this->getField('status'),
			$this->getField('id'),
			$this->getField('photos'),
			$this->getField('area_floor_full')
		];

		return \app\models\OfferMix::find()
		                           ->search()
		                           ->select($select)
		                           ->groupBy($this->getField('object_id'));
	}

	/**
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 * @throws ValidationErrorHttpException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = $this->getQuery();

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 50,
				'pageSizeLimit'   => [0, 50],
			],
		]);

		$this->load($params, '');

		if (!$this->validate()) {
			throw new ValidationErrorHttpException('SSSS');
		}

		$this->normalizeProps();
		$this->setFilters($query);

		return $dataProvider;
	}
}
