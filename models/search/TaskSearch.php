<?php

namespace app\models\search;

use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\common\models\Form\Form;
use app\models\Task;
use app\models\TaskObserver;
use app\models\TaskTag;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class TaskSearch extends Form
{
	public $id;
	public $user_id;
	public $message;
	public $status;
	public $created_by_id;
	public $observer_id;
	public $deleted;
	public $expired;
	public $completed;
	public $multiple;
	public $tag_ids;

	public int $current_user_id;


	public function rules(): array
	{
		return [
			[['id', 'user_id', 'created_by_id', 'observer_id'], 'integer'],
			[['deleted', 'expired', 'completed', 'multiple'], 'boolean'],
			['status', 'each', 'rule' => ['integer']],
			[['message', 'start', 'end', 'created_by_type'], 'safe'],
			['tag_ids', 'each', 'rule' => [
				'exist',
				'targetClass'     => TaskTag::class,
				'targetAttribute' => ['tag_ids' => 'id'],
			]]
		];
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function search(array $params): ActiveDataProvider
	{
		$query = Task::find()
		             ->with(['user.userProfile', 'createdByUser.userProfile', 'tags', 'observers.user.userProfile'])
		             ->joinWith(['tags', 'targetUserObserver tuo'])
		             ->notDeleted();

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => 100
			],
			'sort'       => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'updated_at' => SORT_DESC,
				],
				'attributes'      => [
					'created_at',
					'updated_at',
					'impossible_to',
					'end',
					'start',
					'viewed_at' => [
						'asc'  => [
							new Expression(
								'CASE WHEN tuo.user_id = :current_user_id AND tuo.viewed_at IS NULL THEN 0 ELSE 1 END ASC',
								[':current_user_id' => $this->current_user_id]
							),
							'updated_at' => SORT_ASC
						],
						'desc' => [
							new Expression(
								'CASE WHEN tuo.user_id = :current_user_id AND tuo.viewed_at IS NULL THEN 0 ELSE 1 END ASC',
								[':current_user_id' => $this->current_user_id]
							),
							'updated_at' => SORT_DESC
						]
					]
				]
			]
		]);

		$this->load($params);

		$this->validateOrThrow();

		if ($this->isFilterTrue($this->deleted)) {
			$query->deleted();
		}

		if ($this->isFilterFalse($this->deleted)) {
			$query->notDeleted();
		}

		if ($this->isFilterTrue($this->expired)) {
			$query->expired();
		}

		if ($this->isFilterFalse($this->expired)) {
			$query->notExpired();
		}

		if ($this->isFilterTrue($this->completed)) {
			$query->completed();
		}

		if ($this->isFilterFalse($this->completed)) {
			$query->notCompleted();
		}

		if ($this->isFilterTrue($this->multiple)) {
			$tasksWithObserver = null;

			if ($this->observer_id) {
				$observerQuery = Task::find()->notDeleted()->joinWith('observers')->andWhere(
					['and',
					 [TaskObserver::getColumn('user_id') => $this->observer_id],
					 ['<>', new Expression(TaskObserver::getColumn('user_id')), new Expression(Task::getColumn('user_id'))]
					]
				);

				$observerQuery->select([
					Task::getColumn('id'),
				])->groupBy(Task::getColumn('id'));

				$tasksWithObserver = $observerQuery->column();

				if (empty($this->user_id) and empty($this->created_by_id)) {
					$query->andWhere([Task::getColumn('id') => $tasksWithObserver]);
				}
			}

			$query->andFilterWhere(['or',
			                        [Task::getColumn('user_id') => $this->user_id],
			                        [Task::getColumn('created_by_id') => $this->created_by_id],
			                        [Task::getColumn('id') => $tasksWithObserver]]);

			$query->andFilterWhere([
				Task::getColumn('id')     => $this->id,
				Task::getColumn('status') => $this->status
			]);
		} else {
			$query->andFilterWhere([
				Task::field('id')            => $this->id,
				Task::field('user_id')       => $this->user_id,
				Task::field('status')        => $this->status,
				Task::field('created_by_id') => $this->created_by_id,
			]);
		}

		$query->andFilterWhere(['or',
		                        ['like', Task::getColumn('message'), $this->message],
		                        ['like', Task::getColumn('id'), $this->message]]);

		$query->andFilterWhere(['in', TaskTag::xfield('id'), $this->tag_ids]);

		return $dataProvider;
	}

	/**
	 * @throws ErrorException
	 */
	private function createObserverRelationConditions(): array
	{
		$conditions = ['or',
		               [TaskObserver::getColumn('id') => null],
		               ['<>', TaskObserver::getColumn('user_id'), new Expression(Task::getColumn('user_id'))]
		];

		return $conditions;
	}
}
