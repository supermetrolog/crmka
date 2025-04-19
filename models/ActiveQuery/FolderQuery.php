<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Folder;
use yii\base\ErrorException;

class FolderQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return Folder[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?Folder
	{
		/** @var ?Folder */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Folder
	{
		/** @var Folder */
		return parent::oneOrThrow($db);
	}

	/**
	 * @throws ErrorException
	 */
	public function byUserId(int $userId): self
	{
		return $this->andWhere([Folder::field('user_id') => $userId]);
	}

	/**
	 * @throws ErrorException
	 */
	public function byMorph(string $morph): self
	{
		return $this->andWhere([Folder::field('morph') => $morph]);
	}
}
