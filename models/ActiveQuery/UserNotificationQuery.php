<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\exceptions\ModelNotFoundException;

/**
 * This is the ActiveQuery class for [[\app\models\Notification\UserNotification]].
 *
 * @see \app\models\Notification\UserNotification
 */
class UserNotificationQuery extends \app\kernel\common\models\AQ\AQ
{

    /**
     * @return \app\models\Notification\UserNotification[]|\yii\db\ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return \app\models\Notification\UserNotification|\yii\db\ActiveRecord|null
	 */
    public function one($db = null): ?\app\models\Notification\UserNotification
    {
        return parent::one($db);
    }

	/**
	 * @return \app\models\Notification\UserNotification|\yii\db\ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): \app\models\Notification\UserNotification
	{
		return parent::oneOrThrow($db);
	}
}
