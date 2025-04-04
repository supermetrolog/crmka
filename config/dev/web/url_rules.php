<?php

use app\components\router\Router;

$router = new Router();

$definition = require __DIR__ . "/../../common/web/routes.php";
$definition($router);

return $router->build();
