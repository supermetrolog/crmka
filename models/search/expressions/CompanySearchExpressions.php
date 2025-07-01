<?php

namespace app\models\search\expressions;

use app\components\ExpressionBuilder\FieldExpressionBuilder;
use app\components\ExpressionBuilder\IfExpressionBuilder;
use app\models\Request;
use app\models\Survey;
use yii\base\ErrorException;
use yii\db\Expression;

class CompanySearchExpressions
{
	public static function recentlyCreatedOrder(string $direction = 'ASC', int $interval = 12): Expression
	{
		return IfExpressionBuilder::create()
		                          ->condition("NOW() BETWEEN company.created_at AND DATE_ADD(company.created_at, INTERVAL $interval HOUR)")
		                          ->left('company.created_at')
		                          ->right('NULL')
		                          ->beforeBuild(fn($expression) => "$expression $direction")
		                          ->build();
	}

	/**
	 * @throws ErrorException
	 */
	public static function byRequestStatusOrder(string $direction = 'ASC'): Expression
	{
		return FieldExpressionBuilder::create()
		                             ->field(Request::field('status'))
		                             ->values(0, 2, 1)
		                             ->beforeBuild(fn($expression) => "$expression $direction")
		                             ->build();
	}

	/**
	 * @throws ErrorException
	 */
	public static function requestRelatedOrder(string $direction = 'ASC'): Expression
	{
		return IfExpressionBuilder::create()
		                          ->condition(Request::field('related_updated_at'))
		                          ->left(Request::field('related_updated_at'))
		                          ->right(Request::field('created_at'))
		                          ->beforeBuild(fn($expression) => "$expression $direction")
		                          ->build();
	}

	public static function surveyDelayedOrder(string $direction = 'ASC', string $alias = 'lps'): Expression
	{
		return IfExpressionBuilder::create()
		                          ->condition("$alias.status = :delayedStatus", ['delayedStatus' => Survey::STATUS_DELAYED])
		                          ->left("$alias.updated_at")
		                          ->right('NULL')
		                          ->beforeBuild(fn($expression) => "$expression $direction")
		                          ->build();
	}

	public static function surveyCompletedOrder(string $direction = 'ASC', string $alias = 'lps'): Expression
	{
		return IfExpressionBuilder::create()
		                          ->condition("$alias.status != :delayedStatus", ['delayedStatus' => Survey::STATUS_DELAYED])
		                          ->left("$alias.completed_at")
		                          ->right('NULL')
		                          ->beforeBuild(fn($expression) => "$expression $direction")
		                          ->build();
	}
}