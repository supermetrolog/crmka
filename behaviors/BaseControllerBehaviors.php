<?php

namespace app\behaviors;

use yii\base\Behavior;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;

class BaseControllerBehaviors extends Behavior
{
    public static function getBaseBehaviors(array $owner_behaviors = [], array $auth_except = ['*']): array
    {
        unset($owner_behaviors['authenticator']);
        $owner_behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['*'],
                'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
            ],
        ];
        $owner_behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => $auth_except,
        ];
        return $owner_behaviors;
    }
}
