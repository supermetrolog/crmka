<?php

declare(strict_types=1);

namespace app\commands;

use app\actions\Seeder\NotificationChannelSeeder;
use yii\console\Controller;

class SeederController extends Controller
{
	public function actions(): array
	{
		return [
			'notification-channel' => NotificationChannelSeeder::class
		];
	}
}