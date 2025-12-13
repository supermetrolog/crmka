<?php

namespace app\enum\Attribute;

use app\enum\AbstractEnum;

class AttributeValueTypeEnum extends AbstractEnum
{
	public const STRING   = 'string';
	public const INT      = 'int';
	public const FLOAT    = 'float';
	public const BOOL     = 'bool';
	public const DATE     = 'date';
	public const DATETIME = 'datetime';
	public const JSON     = 'json';
}