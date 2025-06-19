<?php

namespace app\enum;

class EffectKind
{
	public const COMPANIES_ON_OBJECT_IDENTIFIED = 'companies-on-object-identified';

	public const OBJECT_HAS_FREE_AREA                        = 'object-has-free-area';
	public const OBJECT_FREE_AREA_MUST_BE_EDITED             = 'object-free-area-must-be-edited';
	public const OBJECT_FREE_AREA_MUST_BE_EDITED_DESCRIPTION = 'object-free-area-must-be-edited-description';

	public const COMPANY_DOES_NOT_WANT_TO_SELL                    = 'company-does-not-want-to-sell';
	public const COMPANY_WANTS_TO_SELL_MUST_BE_EDITED             = 'company-wants-to-sell-must-be-edited';
	public const COMPANY_WANTS_TO_SELL_MUST_BE_EDITED_DESCRIPTION = 'company-wants-to-sell-must-be-edited-description';

	public const HAS_ACTUAL_REQUESTS = 'has-actual-requests';

	public const HAS_EQUIPMENTS_OFFERS               = 'has-equipments-offers';
	public const HAS_EQUIPMENTS_OFFERS_DESCRIPTION   = 'has-equipments-offers-description';
	public const HAS_EQUIPMENTS_REQUESTS             = 'has-equipments-requests';
	public const HAS_EQUIPMENTS_REQUESTS_DESCRIPTION = 'has-equipments-requests-description';

	public const HAS_NEW_OFFERS   = 'has-new-offers';
	public const HAS_NEW_REQUESTS = 'has-new-requests';

	public const CURRENT_REQUESTS_STEP = 'current-requests-step';
	public const CREATED_REQUESTS_STEP = 'created-requests-step';
	public const CURRENT_OFFERS_STEP   = 'current-offers-step';
	public const CREATED_OFFERS_STEP   = 'created-offers-step';
}