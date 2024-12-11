<?php

namespace app\usecases\TaskHistory;

use app\models\TaskTag;
use app\models\User;
use Yii;
use yii\caching\CacheInterface;

class RelatedDataProvider
{
	private int $cacheDuration;

	private CacheInterface $cache;

	public function __construct(?CacheInterface $cache = null, int $cacheDuration = 300)
	{
		$this->cache         = $cache ?? Yii::$app->cache;
		$this->cacheDuration = $cacheDuration;
	}

	public function getUsers(array $ids): array
	{
		if (empty($ids)) {
			return [];
		}

		return $this->cache->getOrSet(
			['users', $ids],
			fn() => User::find()->byIds($ids)->indexBy('id')->all(),
			$this->cacheDuration
		);
	}

	public function getTags(array $ids): array
	{
		if (empty($ids)) {
			return [];
		}

		return $this->cache->getOrSet(
			['task_tags', $ids],
			fn() => TaskTag::find()->byIds($ids)->indexBy('id')->all(),
			$this->cacheDuration
		);
	}
}