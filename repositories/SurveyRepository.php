<?php

declare(strict_types=1);

namespace app\repositories;

use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Call;
use app\models\Survey;
use app\models\Task;
use app\models\TaskRelationEntity;
use app\models\User\User;
use app\models\views\ChatMemberStatisticView;
use app\models\views\UserSurveyStatisticView;
use DateTimeInterface;
use yii\base\ErrorException;
use yii\db\Expression;

class SurveyRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Survey
	{
		return Survey::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneByIdWithRelationsOrThrow(int $id): Survey
	{
		return Survey::find()
		             ->byId($id)
		             ->with([
			             'tasks.user.userProfile',
			             'tasks.tags',
			             'tasks.createdByUser.userProfile',
			             'tasks.observers.user.userProfile',
			             'tasks.targetUserObserver',
			             'actions.createdBy.userProfile'
		             ])
		             ->with([
			             'dependentSurveys.chatMember.objectChatMember.object.company',
			             'dependentSurveys.chatMember.objectChatMember.object.consultant.userProfile',
			             'dependentSurveys.chatMember.objectChatMember.object.offers',
			             'dependentSurveys.chatMember.company.consultant.userProfile',
			             'dependentSurveys.chatMember.company.categories',
			             'dependentSurveys.chatMember.company.companyGroup',
			             'dependentSurveys.user.userProfile',
			             'dependentSurveys.contact.consultant.userProfile',
			             'dependentSurveys.contact.emails',
			             'dependentSurveys.contact.phones',
			             'dependentSurveys.contact.websites',
			             'dependentSurveys.contact.wayOfInformings'
		             ])
		             ->with(['calls'])
		             ->oneOrThrow();
	}

	public function findPendingByChatMemberIdAndUserId(int $chatMemberId, int $userId): ?Survey
	{
		return Survey::find()->pending()->byChatMemberId($chatMemberId)->byUserId($userId)->notDeleted()->one();
	}

	/**
	 * @return ChatMemberStatisticView[]
	 * @throws ErrorException
	 */
	public function getStatistic(DateTimeInterface $after, DateTimeInterface $before): array
	{
		$afterDate  = DateTimeHelper::format($after);
		$beforeDate = DateTimeHelper::format($before);

		$query = UserSurveyStatisticView::find()
		                                ->select([
			                                '*',
			                                'surveys_count'         => new Expression('COALESCE(srv.surveys_count, 0)'),
			                                'survey_tasks_count'    => new Expression('COALESCE(stsk.tasks_count, 0)'),
			                                'completed_tasks_count' => new Expression('COALESCE(ctsk.completed_tasks_count, 0)'),
			                                'calls_total_count'     => new Expression('COALESCE(cll.calls_total, 0)'),
			                                'calls_accepted_count'  => new Expression('COALESCE(cll.calls_accepted, 0)'),
			                                'calls_rejected_count'  => new Expression('COALESCE(cll.calls_rejected, 0)'),
		                                ])
		                                ->andWhere([
			                                User::field('status') => User::STATUS_ACTIVE,
			                                User::field('role')   => [User::ROLE_CONSULTANT, User::ROLE_MODERATOR, User::ROLE_OWNER]
		                                ])
		                                ->with('userProfile');

		$surveyQuery = Survey::find()
		                     ->select([
			                     'user_id',
			                     'surveys_count' => new Expression('COUNT(*)')
		                     ])
		                     ->notDeleted()
		                     ->byStatuses([Survey::STATUS_COMPLETED, Survey::STATUS_CANCELED])
		                     ->byType(Survey::TYPE_ADVANCED)
		                     ->andWhere(['>=', 'created_at', $afterDate])
		                     ->andWhere(['<=', 'created_at', $beforeDate])
		                     ->groupBy('user_id');

		$query->leftJoin(['srv' => $surveyQuery], 'srv.user_id = user.id');

		$callQuery = Call::find()
		                 ->select([
			                 'user_id',
			                 'calls_total'    => new Expression('COUNT(DISTINCT id)'),
			                 'calls_accepted' => new Expression('SUM(CASE WHEN status = :status THEN 1 ELSE 0 END)', [':status' => Call::STATUS_COMPLETED]),
			                 'calls_rejected' => new Expression('SUM(CASE WHEN status != :status THEN 1 ELSE 0 END)', [':status' => Call::STATUS_COMPLETED])
		                 ])
		                 ->andWhere(['>=', 'created_at', $afterDate])
		                 ->andWhere(['<=', 'created_at', $beforeDate])
		                 ->andWhereNull('deleted_at')
		                 ->groupBy('user_id');

		$query->leftJoin(['cll' => $callQuery], 'cll.user_id = user.id');

		$taskSurveyQuery = TaskRelationEntity::find()
		                                     ->alias('tre')
		                                     ->select([
			                                     'sv.user_id',
			                                     'tasks_count' => new Expression('COUNT(DISTINCT tre.task_id)'),
		                                     ])
		                                     ->innerJoin(['sv' => Survey::tableName()], "tre.entity_type = :surveyMorph AND tre.entity_id = sv.id AND sv.deleted_at IS NULL", ['surveyMorph' => Survey::getMorphClass()])
		                                     ->andWhere(['tre.deleted_at' => null])
		                                     ->andWhere(['sv.type' => Survey::TYPE_ADVANCED])
		                                     ->andWhere(['sv.status' => [Survey::STATUS_COMPLETED, Survey::STATUS_CANCELED]])
		                                     ->andWhere(['>=', 'sv.created_at', $afterDate])
		                                     ->andWhere(['<=', 'sv.created_at', $beforeDate])
		                                     ->groupBy('sv.user_id');

		$query->leftJoin(['stsk' => $taskSurveyQuery], 'stsk.user_id = user.id');

		$completedTaskQuery = Task::find()
		                          ->select(['user_id', 'completed_tasks_count' => new Expression('COUNT(*)')])
		                          ->notDeleted()
		                          ->byStatus(Task::STATUS_DONE)
		                          ->andWhere(['>=', 'updated_at', $afterDate])
		                          ->andWhere(['<=', 'updated_at', $beforeDate])
		                          ->groupBy('user_id');

		$query->leftJoin(['ctsk' => $completedTaskQuery], 'ctsk.user_id = user.id');

		return $query->all();
	}
}