<?php

declare(strict_types=1);

namespace app\models\company\eventslog;

use app\exceptions\ValidationErrorHttpException;

class CreateCompanyEvent extends CompanyEventsLog
{
    public function create(): void
    {
        if (!$this->validate() || !$this->save()) {
            throw new ValidationErrorHttpException($this->getErrorSummary(false));
        }
    }
}
