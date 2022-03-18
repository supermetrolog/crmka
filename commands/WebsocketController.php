<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\daemons\ServerWS;
use consik\yii2websocket\WebSocketServer;

class WebsocketController extends Controller
{
    public function actionIndex()
    {
        echo "FUCK";
        return ExitCode::OK;
    }
    public function actionStart($expand = "caller,phoneFrom,phoneFrom.contact,phoneTo,phoneTo.contact")
    {
        $server = new ServerWS();
        $server->port = 8082; //This port must be busy by WebServer and we handle an error
        // $server->loop->addPeriodicTimer($this->timeout, function () use ($server) {
        //     echo "\nTimer!\n";
        //     $server->checkUpdates();
        // });
        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN_ERROR, function ($e) use ($server) {
            echo "Error opening port " . $server->port . "\n";
            $server->port += 1; //Try next port to open
            $server->start();
        });

        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN, function ($e) use ($server) {
            echo "Server started at port " . $server->port;
        });
        $server->on(WebSocketServer::EVENT_CLIENT_CONNECTED, function ($e) use ($server) {
            echo "\nCLIENT CONNECTED\n";
            $e->client->send(json_encode(['message' => 'fuck you']));
        });
        echo "Start server\n";
        $server->start();
        echo "\nStop server";
    }
}
