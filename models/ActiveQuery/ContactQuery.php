<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Contact;
use yii\db\ActiveRecord;

class ContactQuery extends AQ
{
	/**
	 * @return Contact[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Contact|ActiveRecord|null
	 */
	public function one($db = null): ?Contact
	{
		return parent::one($db);
	}

	/**
	 * @return Contact|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Contact
	{
		return parent::oneOrThrow($db);
	}

	/**
	 * @param ?int $id
	 *
	 * @return self
	 */
	public function byId(?int $id): self
	{
		return $this->andWhere([$this->field('id') => $id]);
	}

	public function byCompanyId(int $id): self
	{
		return $this->andWhere([$this->field('company_id') => $id]);
	}

	public function general(): self
	{
		return $this->andWhere([$this->field('type') => Contact::GENERAL_CONTACT_TYPE]);
	}

	public function main(): self
	{
		return $this->andWhere([$this->field('isMain') => Contact::IS_MAIN_CONTACT]);
	}
}
