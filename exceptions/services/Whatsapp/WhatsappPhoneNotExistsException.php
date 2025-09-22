<?php

namespace app\exceptions\services\Whatsapp;

use DomainException;

class WhatsappPhoneNotExistsException extends DomainException
{
	protected $message = 'Phone not exists';
}