<?php

namespace app\daemons;

use app\components\ConsoleLogger;
use app\helpers\ArrayHelper;
use Exception;
use Ratchet\ConnectionInterface;
use yii\base\Model;

class Clients extends Model
{
	private array $pools = [];

	/**
	 * @throws Exception
	 */
	public function add(ConnectionInterface $connection): void
	{
		[$userId, $windowId] = $this->extractIdentity($connection);

		if ($userId === null || $windowId === null) {
			ConsoleLogger::info('skip add: missing identity on connection');

			return;
		}

		$this->pools[$userId]            ??= [];
		$this->pools[$userId][$windowId] = $connection;

		ConsoleLogger::info("ws add user={$userId}, window={$windowId}, total_for_user=" . ArrayHelper::length($this->pools[$userId]));
	}

	/**
	 * @throws Exception
	 */
	public function remove(ConnectionInterface $connection): bool
	{
		[$userId, $windowId] = $this->extractIdentity($connection);

		if ($userId === null || $windowId === null) {
			ConsoleLogger::info('skip remove: missing identity on connection');

			return false;
		}

		if (!isset($this->pools[$userId][$windowId])) {
			ConsoleLogger::info("skip remove: not found user={$userId}, window={$windowId}");

			return false;
		}

		unset($this->pools[$userId][$windowId]);

		if (ArrayHelper::empty($this->pools[$userId])) {
			unset($this->pools[$userId]);
		}

		ConsoleLogger::info("ws remove user={$userId}, window={$windowId}");

		return true;
	}

	public function send(ConnectionInterface $connection, Message $message): ConnectionInterface
	{
		return $connection->send($message->getData());
	}

	/**
	 * @throws Exception
	 */
	public function sendToUser(int $userId, Message $msg, ?ConnectionInterface $excludeConnection = null): bool
	{
		if (!isset($this->pools[$userId])) {
			ConsoleLogger::info("sendToUser: no sessions for user={$userId}");

			return false;
		}

		$excludeWindow = $excludeConnection ? ($this->extractIdentity($excludeConnection)[1] ?? null) : null;

		foreach ($this->pools[$userId] as $windowId => $conn) {
			if ($excludeWindow !== null && $windowId === $excludeWindow) {
				continue;
			}

			$this->send($conn, $msg);
		}

		return true;
	}

	/**
	 * @throws Exception
	 */
	public function broadcast(Message $msg, ?ConnectionInterface $excludeConnection = null): void
	{
		ConsoleLogger::info('broadcast to all users');

		[$excludeUser, $excludeWindow] = $excludeConnection ? $this->extractIdentity($excludeConnection) : [null, null];

		foreach ($this->pools as $userId => $connections) {
			foreach ($connections as $windowId => $conn) {
				if ($excludeUser !== null && $excludeWindow !== null
				    && $userId === $excludeUser && $windowId === $excludeWindow) {
					continue;
				}

				$this->send($conn, $msg);
			}
		}

		ConsoleLogger::info('broadcast done');
	}

	public function hasUser(int $userId): bool
	{
		return isset($this->pools[$userId]) && ArrayHelper::notEmpty($this->pools[$userId]);
	}

	/**
	 * @throws Exception
	 */
	public function hasConnection(ConnectionInterface $connection): bool
	{
		[$userId, $windowId] = $this->extractIdentity($connection);

		return $userId !== null && $windowId !== null && isset($this->pools[$userId][$windowId]);
	}

	/**
	 * @throws Exception
	 */
	private function extractIdentity(ConnectionInterface $conn): array
	{
		$payload = $conn->name ?? [];

		return [
			ArrayHelper::getValue($payload, 'user_id'),
			ArrayHelper::getValue($payload, 'window_id')
		];
	}
}
