<?php

namespace app\exceptions\services\Telegram;

use DomainException;

class UserTelegramTicketIsConsumedException extends DomainException
{
	protected $message = 'Ticket is consumed';
}