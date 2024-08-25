<?php

declare(strict_types=1);

namespace app\kernel\common\models\AR\ManyToManyTrait;

use app\kernel\common\models\AR\AR;

/**
 * @mixin AR
 */
trait ManyToManyTrait
{
	use ManyToManyUpdateTrait;
	use ManyToManyUnlinkTrait;
	use ManyToManyLinkTrait;
}