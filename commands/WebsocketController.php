<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\ConsoleLogger;
use yii\console\Controller;
use app\daemons\ServerWS;
use consik\yii2websocket\WebSocketServer;

class WebsocketController extends Controller
{
    public function actionStart($expand = "caller,phoneFrom,phoneFrom.contact,phoneTo,phoneTo.contact")
    {
        $server = new ServerWS();
        $server->port = 8010; // Default port
        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN_ERROR, function ($e) use ($server) {
            ConsoleLogger::info("error opening port " . $server->port);
            $server->port += 1; //Try next port to open
            $server->start();
        });

        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN, function () use ($server) {
            ConsoleLogger::info("server started at port " . $server->port . "...");
        });
        $server->on(WebSocketServer::EVENT_CLIENT_CONNECTED, function ($e) use ($server) {
            ConsoleLogger::info("client connected");
            $e->client->name = null;
            $e->client->send(json_encode(['message' => 'Client connected']));
        });

        ConsoleLogger::info("start server");
        $server->start();
        ConsoleLogger::info("stop server");
    }
}
