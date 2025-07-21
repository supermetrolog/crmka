<?php

namespace app\models\ActiveQuery;

use app\enum\Request\RequestStatusEnum;
use app\kernel\common\models\AQ\AQ;
use app\models\Request;
use yii\base\ErrorException;

class RequestQuery extends AQ
{

	/** @return Request[] */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?Request
	{
		/** @var ?Request */
		return parent::one($db);
	}

	public function oneOrThrow($db = null): Request
	{
		/** @var Request */
		return parent::oneOrThrow($db);
	}

	/**
	 * @throws ErrorException
	 */
	public function active(): self
	{
		return $this->andWhere([Request::field('status') => RequestStatusEnum::ACTIVE]);
	}

	/**
	 * @throws ErrorException
	 */
	public function byCompanyId(int $id): self
	{
		return $this->andWhere([Request::field('company_id') => $id]);
	}
}
