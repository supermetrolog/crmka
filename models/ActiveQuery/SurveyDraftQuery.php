<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Survey;
use app\models\SurveyDraft;

/**
 * This is the ActiveQuery class for [[\app\models\Surveys]].
 *
 * @see Survey
 */
class SurveyDraftQuery extends AQ
{
	/** @return SurveyDraft[] */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?SurveyDraft
	{
		/** @var ?SurveyDraft */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): SurveyDraft
	{
		/** @var SurveyDraft */
		return parent::oneOrThrow($db);
	}

	public function byUserId(int $userId): SurveyDraftQuery
	{
		return $this->andWhere([$this->field('user_id') => $userId]);
	}

	public function byChatMemberId(int $chatMemberId): SurveyDraftQuery
	{
		return $this->andWhere([$this->field('chat_member_id') => $chatMemberId]);
	}
}
