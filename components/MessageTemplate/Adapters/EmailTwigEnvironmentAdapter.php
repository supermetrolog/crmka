<?php

declare(strict_types=1);

namespace app\components\MessageTemplate\Adapters;

use app\components\MessageTemplate\Interfaces\EmailTwigEnvironmentInterface;
use Twig\Environment;

class EmailTwigEnvironmentAdapter extends Environment implements EmailTwigEnvironmentInterface
{
} 