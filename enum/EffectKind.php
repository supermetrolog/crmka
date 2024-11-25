<?php

namespace app\enum;

class EffectKind
{
	public const COMPANY_PLANNED_DEVELOP        = 'company-planned-develop';
	public const REQUESTS_NO_LONGER_RELEVANT    = 'requests-no-longer-relevant';
	public const COMPANIES_ON_OBJECT_IDENTIFIED = 'companies-on-object-identified';
	public const COMPANY_HAS_NEW_REQUEST        = 'company-has-new-request';
	public const COMPANY_WANTS_TO_BUY_OR_BUILD  = 'company-wants-to-buy-or-build';
	public const COMPANY_WANTS_TO_SELL          = 'company-wants-to-sell';
}