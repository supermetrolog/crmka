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
use Yii;

class WebsocketController extends Controller
{
    public function actionIndex()
    {
        $i = 0;
        while (true) {
            $i++;
            file_put_contents(Yii::getAlias('@app') . '/public_html/tmp/test.txt', "FUCK " . $i . "\n", FILE_APPEND);
            echo "FUCK " . $i . "\n";
            sleep(3);
        }
        return ExitCode::OK;
    }
    public function actionStart($expand = "caller,phoneFrom,phoneFrom.contact,phoneTo,phoneTo.contact")
    {
        foreach (\Yii::$app->log->targets as $target) {
            $target->setEnabled(false);
        }
        $server = new ServerWS();
        $server->port = 8010; //This port must be busy by WebServer and we handle an error
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
            $e->client->name = null;
            $e->client->send(json_encode(['message' => 'Client connected']));
        });

        echo "Start server\n";
        $server->start();
        echo "\nStop server";
    }
}
