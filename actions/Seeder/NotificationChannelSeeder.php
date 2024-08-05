<?php

declare(strict_types=1);

namespace app\actions\Seeder;

use app\models\Notification\NotificationChannel;
use yii\base\Action;
use yii\db\Exception;

class NotificationChannelSeeder extends Action
{

	/**
	 * @throws Exception
	 */
	public function run(): void
	{
		$channels = [
			[
				'slug'       => NotificationChannel::WEB,
				'name'       => NotificationChannel::WEB,
				'is_enabled' => true,
			],
			[
				'slug'       => NotificationChannel::EMAIL,
				'name'       => NotificationChannel::EMAIL,
				'is_enabled' => false,
			],
		];

		foreach ($channels as $channel) {
			NotificationChannel::upsert($channel, $channel);
		}
	}
}