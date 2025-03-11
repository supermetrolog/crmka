<?php

namespace app\enum;

class EffectKind
{
	public const COMPANY_PLANNED_DEVELOP                          = 'company-planned-develop';
	public const REQUESTS_NO_LONGER_RELEVANT                      = 'requests-no-longer-relevant';
	public const COMPANIES_ON_OBJECT_IDENTIFIED                   = 'companies-on-object-identified';
	public const COMPANY_HAS_NEW_REQUEST                          = 'company-has-new-request';
	public const COMPANY_WANTS_TO_BUY_OR_BUILD                    = 'company-wants-to-buy-or-build';
	public const COMPANY_WANTS_TO_SELL                            = 'company-wants-to-sell';
	public const COMPANY_DOES_NOT_WANT_TO_SELL                    = 'company-does-not-want-to-sell';
	public const COMPANY_WANTS_TO_SELL_MUST_BE_EDITED             = 'company-wants-to-sell-must-be-edited';
	public const COMPANY_WANTS_TO_SELL_MUST_BE_EDITED_DESCRIPTION = 'company-wants-to-sell-must-be-edited-description';
	public const COMPANY_WANTS_TO_BUY_OR_SELL_EQUIPMENT           = 'company-wants-to-buy-or-sell-equipment';
	public const OBJECT_HAS_EQUIPMENT_FOR_SALE                    = 'object-has-equipment-for-sale';
	public const OBJECT_HAS_FREE_AREA                             = 'object-has-free-area';
	public const COMPANY_HAS_SUBLEASE_OR_STORAGE                  = 'company-has-sublease-or-storage';
	public const OBJECT_FREE_AREA_ALREADY_DESCRIBED               = 'object-free-area-already-described';
	public const OBJECT_FREE_AREA_MUST_BE_EDITED                  = 'object-free-area-must-be-edited';
	public const OBJECT_FREE_AREA_MUST_BE_EDITED_DESCRIPTION      = 'object-free-area-must-be-edited-description';
	public const OBJECT_FREE_AREA_MUST_BE_DELETED                 = 'object-free-area-must-be-deleted';

	public const HAS_ACTUAL_REQUESTS = 'has-actual-requests';
	public const HAS_ACTUAL_OFFERS   = 'has-actual-offers';
}