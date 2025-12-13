<?php

namespace app\enum\Attribute;

use app\enum\AbstractEnum;

class AttributeInputTypeEnum extends AbstractEnum
{
	public const TEXT        = 'text';
	public const NUMBER      = 'number';
	public const EMAIL       = 'email';
	public const PHONE       = 'phone';
	public const URL         = 'url';
	public const DATE        = 'date';
	public const TIME        = 'time';
	public const CHECKBOX    = 'checkbox';
	public const RADIO       = 'radio';
	public const SELECT      = 'select';
	public const MULTISELECT = 'multiselect';
	public const CUSTOM      = 'custom';
}