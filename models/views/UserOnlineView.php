<?php

namespace app\models\views;

use app\models\User;

class UserOnlineView extends User
{
	public int $online_count = 0;
}
