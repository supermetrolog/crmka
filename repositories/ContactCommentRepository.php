<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\miniModels\ContactComment;

class ContactCommentRepository
{
	/**
	 * @return ContactComment[]
	 */
	public function findAllByContactId(int $id): array
	{
		return ContactComment::find()->andWhere(['contact_id' => $id])->all();
	}

	/**
	 * @return ContactComment[]
	 */
	public function findModelByAuthorId(int $id): array
	{
		return ContactComment::find()->andWhere(['author_id' => $id])->all();
	}

	public function findModelById(int $id): ?ContactComment
	{
		return ContactComment::findOne($id);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdOrThrow(int $id): ContactComment
	{
		/** @var ContactComment */
		return ContactComment::find()->byId($id)->oneOrThrow();
	}
}