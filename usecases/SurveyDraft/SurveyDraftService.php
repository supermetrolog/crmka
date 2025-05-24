<?php

declare(strict_types=1);

namespace app\usecases\SurveyDraft;

use app\dto\SurveyDraft\CreateSurveyDraftDto;
use app\dto\SurveyDraft\UpdateSurveyDraftDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\SurveyDraft;
use app\repositories\SurveyDraftRepository;
use Throwable;
use yii\db\StaleObjectException;
use yii\helpers\Json;

class SurveyDraftService
{
	private SurveyDraftRepository $repository;

	public function __construct(
		SurveyDraftRepository $repository
	)
	{
		$this->repository = $repository;
	}

	/**
	 * @throws SaveModelException
	 */
	public function createOrUpdate(CreateSurveyDraftDto $dto): SurveyDraft
	{
		$draft = $this->repository->findOneByChatMemberIdAndUserId($dto->chatMember->id, $dto->user->id);

		if ($draft) {
			return $this->update($draft, new UpdateSurveyDraftDto([
				'data' => $dto->data
			]));
		}

		return $this->create($dto);
	}

	/**
	 * @throws SaveModelException
	 */
	public function create(CreateSurveyDraftDto $dto): SurveyDraft
	{
		$model = new SurveyDraft([
			'user_id'        => $dto->user->id,
			'chat_member_id' => $dto->chatMember->id,
			'data'           => Json::encode($dto->data)
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(SurveyDraft $model, UpdateSurveyDraftDto $dto): SurveyDraft
	{
		$model->load([
			'data' => Json::encode($dto->data)
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(SurveyDraft $model): void
	{
		$model->delete();
	}
}