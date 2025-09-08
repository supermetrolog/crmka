<?php

declare(strict_types=1);

namespace app\components;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use yii\base\Component;
use yii\helpers\Json;

class NotificationsQueueService extends Component
{
	private AMQPChannel          $channel;
	private AMQPStreamConnection $connection;

	public string $host;
	public int    $port;
	public string $user;
	public string $password;
	public string $queueName;
	public string $exchangeName;


	/**
	 * @throws Exception
	 */
	public function init(): void
	{
		parent::init();

		$this->connection = new AMQPStreamConnection(
			$this->host,
			$this->port,
			$this->user,
			$this->password
		);

		$this->channel = $this->connection->channel();

		$this->channel->exchange_declare($this->exchangeName, AMQPExchangeType::DIRECT, false, true, false);

		$this->channel->queue_declare($this->queueName, false, true, false, false);
		$this->channel->queue_bind($this->queueName, $this->exchangeName);
	}

	/**
	 * @throws Exception
	 */
	public function __destruct()
	{
		$this->close();
	}

	public function getChannel(): AMQPChannel
	{
		return $this->channel;
	}

	public function getConnection(): AMQPStreamConnection
	{
		return $this->connection;
	}

	/**
	 * @throws Exception
	 */
	public function close(): void
	{
		if ($this->channel->is_open()) {
			$this->channel->close();
		}

		if ($this->connection->isConnected()) {
			$this->connection->close();
		}
	}

	public function get(): ?NotifyQueueMessageDecorator
	{
		$message = $this->channel->basic_get($this->queueName);

		if (!$message) {
			return null;
		}

		return new NotifyQueueMessageDecorator($message);
	}

	public function publish(array $payload): void
	{
		$message = new AMQPMessage(
			Json::encode($payload),
			[
				'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
				'content_type'  => 'application/json',
				'timestamp'     => time()
			]
		);

		$this->channel->basic_publish($message, $this->exchangeName);
	}
}
