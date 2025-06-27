<?php

declare(strict_types=1);

namespace app\dto\UserTour;

use app\models\User;
use yii\base\BaseObject;

class UserTourViewDto extends BaseObject
{
	public User   $user;
	public string $tour_id;
	public string $steps_viewed;
	public string $steps_total;

}