<?php

namespace app\commands;

use app\components\ConsoleLogger;
use app\daemons\ServerWS;
use consik\yii2websocket\WebSocketServer;
use yii\console\Controller;

class WebsocketController extends Controller
{
	public function actionStart($expand = "caller,phoneFrom,phoneFrom.contact,phoneTo,phoneTo.contact"): void
	{
		$server = new ServerWS();

		$server->port = 8010; // Default port

		$server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN_ERROR, function ($e) use ($server) {
			ConsoleLogger::info("error opening port " . $server->port . " with message: ");

			throw $e->exception;
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
