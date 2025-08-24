<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Templates;

use app\components\MessageTemplate\Interfaces\MessageTemplateContextInterface;
use yii\base\BaseObject;

abstract class AbstractTemplateContext extends BaseObject implements MessageTemplateContextInterface
{
}