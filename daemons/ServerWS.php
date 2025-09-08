<?php

namespace app\daemons;

use app\components\ConsoleLogger;
use app\daemons\loops\notification\NotifyLoop;
use consik\yii2websocket\events\ExceptionEvent;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Yii;
use yii\helpers\Json;

class ServerWS extends WebSocketServer
{
	public const MESSAGE_TEMPLATE = ['message' => '', 'action' => 'info', 'error' => false, 'success' => false];
	private Clients $_clients;
	private int     $timeout = 2; // seconds

	public function start(): bool
	{
		try {
			$this->server = IoServer::factory(
				new HttpServer(
					new WsServer(
						$this
					)
				),
				$this->port
			);

			$this->trigger(self::EVENT_WEBSOCKET_OPEN);

			$this->clients = new \SplObjectStorage();

			$this->initServerData();

			$this->server->run();

			return true;
		} catch (\Exception $e) {
			$errorEvent = new ExceptionEvent([
				'exception' => $e
			]);
			$this->trigger(self::EVENT_WEBSOCKET_OPEN_ERROR, $errorEvent);

			return false;
		}
	}

	public function initServerData(): void
	{
		$this->_clients = new Clients();

		$loop = new NotifyLoop(Yii::$app->notifyQueue);

		$this->server->loop->addPeriodicTimer($this->timeout, function () use ($loop) {
			try {
				$loop->run($this->_clients);
			} catch (yii\db\Exception $e) {
				Yii::$app->db->close();
				Yii::$app->db->open();

				ConsoleLogger::info($e->getMessage());

				$loop->run($this->_clients);
			}
		});

		$this->on(WebSocketServer::EVENT_CLIENT_DISCONNECTED, function ($e) {
			ConsoleLogger::info("client disconnected");

			$this->_clients->remove($e->client);
		});
	}

	protected function getCommand(ConnectionInterface $from, $msg)
	{
		$request = Json::decode($msg);

		return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
	}

	public function commandPing(ConnectionInterface $client, $msg): ConnectionInterface
	{
		$message = new Message();

		$message->setAction('pong');
		$message->setBody("pong");

		return $this->_clients->send($client, $message);
	}

	public function commandEcho(ConnectionInterface $client, $msg): void
	{
		ConsoleLogger::info('command echo');

		$client->send($msg);
	}

	/**
	 * @throws \Exception
	 */
	public function commandSendPool(ConnectionInterface $client, $msg): bool
	{
		ConsoleLogger::info('command: send pool');

		$msg = json_decode($msg);

		$message = new Message();

		$message->setBody($msg->data->message);
		$message->setAction($msg->data->action);

		return $this->_clients->sendToUser($client->name->user_id, $message);
	}

	/**
	 * @throws \Throwable
	 */
	public function commandSetUser(ConnectionInterface $client, $msg): ConnectionInterface
	{
		try {
			ConsoleLogger::info('command: set user');

			$msg = json_decode($msg);

			$message = new Message();

			$message->setAction('user_set');

			if ($this->_clients->hasConnection($client)) {
				$message->setError();
				$message->setBody("You already registered in websocket! ({$client->name->user_id})");

				return $this->_clients->send($client, $message);
			}

			$client->name = $msg->data;

			$this->_clients->add($client);

			$message->setBody("You successfully registered ({$client->name->user_id})");

			return $this->_clients->send($client, $message);
		} catch (\Throwable $th) {
			ConsoleLogger::info($th->getMessage());

			throw $th;
		}
	}
}
