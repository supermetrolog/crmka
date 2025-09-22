<?php

namespace app\exceptions\services\Telegram;

use DomainException;

class UserTelegramTicketIsExpiredException extends DomainException
{
	protected $message = 'Ticket is expired';
}