<?php

declare(strict_types=1);

namespace app\usecases\Contact;

use app\dto\ContactComment\CreateContactCommentDto;
use app\dto\ContactComment\UpdateContactCommentDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\ContactComment;
use Throwable;
use yii\db\StaleObjectException;

class ContactCommentService
{
	/**
	 * @param CreateContactCommentDto $dto
	 *
	 * @return ContactComment
	 * @throws SaveModelException
	 */
	public function create(CreateContactCommentDto $dto): ContactComment
	{
		$model = new ContactComment([
			'contact_id' => $dto->contact_id,
			'author_id'  => $dto->author_id,
			'comment'    => $dto->comment
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @param ContactComment          $model
	 * @param UpdateContactCommentDto $dto
	 *
	 * @return ContactComment
	 * @throws SaveModelException
	 */
	public function update(ContactComment $model, UpdateContactCommentDto $dto): ContactComment
	{
		$model->load([
			'comment' => $dto->comment
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @param ContactComment $model
	 *
	 * @return void
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(ContactComment $model): void
	{
		$model->delete();
	}
}