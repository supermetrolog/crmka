<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\SurveyDraft;

class SurveyDraftRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): SurveyDraft
	{
		return SurveyDraft::find()->byId($id)->oneOrThrow();
	}

	public function findOneByChatMemberIdAndUserId(int $chatMemberId, int $userId): ?SurveyDraft
	{
		return SurveyDraft::find()->byChatMemberId($chatMemberId)->byUserId($userId)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneByChatMemberIdAndUserIdOrThrow(int $chatMemberId, int $userId): SurveyDraft
	{
		return SurveyDraft::find()->byChatMemberId($chatMemberId)->byUserId($userId)->oneOrThrow();
	}

	/** @return SurveyDraft[] */
	public function findAllByUserId(int $userId): array
	{
		return SurveyDraft::find()->byUserId($userId)->all();
	}
}