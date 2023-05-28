<?php

use yii\BaseYii;
use yii\db\Connection;
use yii\web\Application;
use yii\web\User;

/**
 * This class only exists here for IDE (PHPStorm/Netbeans/...) autocompletion.
 * This file is never included anywhere.
 * Adjust this file to match classes configured in your application config, to enable IDE autocompletion for custom components.
 * Example: A property phpdoc can be added in `__Application` class as `@property \vendor\package\Rollbar|__Rollbar $rollbar` and adding a class in this file
 * ```php
 * // @property of \vendor\package\Rollbar goes here
 * class __Rollbar {
 * }
 * ```
 */
class Yii extends BaseYii
{
    /**
     * @var Application|\yii\console\Application|__Application
     */
    public static $app;
}

/**
 * @property yii\rbac\DbManager $authManager
 * @property User|__WebUser $user
 * @property yii\base\Security|__Security $security
 * @property array $params
 * @property Connection $db
 * @property Connection $db_old
 *
 */
class __Application extends Application
{
}

// class __Response extends Response
// {
//     public function getStatusCode()
//     {
//     }
// }

/**
 * @property app\models\User $identity
 */
class __WebUser
{
}

class __Security
{
}