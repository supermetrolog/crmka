<?php

namespace app\behaviors;

use app\exceptions\http\RestrictedIpHttpException;
use app\helpers\ArrayHelper;
use app\models\User\User;
use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class IpRestrictionFilter extends ActionFilter
{
	public array $allowedOfficeIps = [];

	/**
	 * @throws ForbiddenHttpException
	 */
	public function beforeAction($action): bool
	{
		/** @var User $user */
		$user = Yii::$app->user->identity;

		if (!$user || !$user->isIpAccessRestricted() || ArrayHelper::empty($this->allowedOfficeIps)) {
			return parent::beforeAction($action);
		}

		$userIp = Yii::$app->request->getUserIP();

		if (!ArrayHelper::includes($this->allowedOfficeIps, $userIp)) {
			Yii::warning("Access denied for user #$user->id from IP $userIp", __METHOD__);

			throw new RestrictedIpHttpException();
		}

		return parent::beforeAction($action);
	}
}