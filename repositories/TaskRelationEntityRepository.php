<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\RequestQuery;
use app\models\ActiveQuery\SurveyQuery;
use app\models\ActiveQuery\TaskQuery;
use app\models\TaskRelationEntity;

class TaskRelationEntityRepository
{
	/**
	 * @return TaskRelationEntity[]
	 */
	public function findAllByTaskId(int $taskId): array
	{
		return TaskRelationEntity::find()->byTaskId($taskId)->all();
	}

	/**
	 * @return TaskRelationEntity[]
	 */
	public function findAllByTaskIdWithRelations(int $taskId): array
	{
		return TaskRelationEntity::find()->byTaskId($taskId)
		                         ->with([
			                         'company'     => function (CompanyQuery $query) {
				                         $query->with([
					                         'logo', 'companyGroup',
					                         'categories', 'consultant.userProfile',
					                         'companyActivityGroups', 'companyActivityProfiles'
				                         ]);
			                         },
			                         'contact'     => function (ContactQuery $query) {
				                         $query->with([
					                         'phones', 'emails', 'wayOfInformings', 'company',
				                         ]);
			                         },
			                         'request'     => function (RequestQuery $query) {
				                         $query->with([
					                         'company', 'consultant.userProfile',
					                         'regions', 'directions', 'districts'
				                         ]);
			                         },
			                         'relatedTask' => function (TaskQuery $query) {
				                         $query->with([
					                         'user.userProfile', 'tags',
					                         'createdByUser.userProfile',
					                         'observers.user.userProfile',
					                         'targetUserObserver'
				                         ]);
			                         },
			                         'offerMix'    => function ($query) {
				                         $query->with(['company']);
			                         },
			                         'object'      => function ($query) {
				                         $query->with(['company']);
			                         },
			                         'survey'      => function (SurveyQuery $query) {
				                         $query->with(['user.userProfile', 'contact', 'calls'])
				                               ->with([
					                               'chatMember.objectChatMember.object.company',
					                               'chatMember.objectChatMember.object.consultant',
					                               'chatMember.objectChatMember.object.offers', 'chatMember.company.categories',
					                               'chatMember.company.companyGroup',
					                               'chatMember.company.consultant.userProfile',
					                               'chatMember.company.categories',
					                               'chatMember.company.companyActivityGroups',
					                               'chatMember.company.companyActivityProfiles',
				                               ]);
			                         }
		                         ])
		                         ->notDeleted()
		                         ->all();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): TaskRelationEntity
	{
		return TaskRelationEntity::find()->byId($id)->oneOrThrow();
	}
}