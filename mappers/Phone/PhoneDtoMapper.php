<?php

namespace app\mappers\Phone;

use app\dto\Phone\PhoneDto;
use app\mappers\AbstractDtoMapper;
use app\models\miniModels\Phone;

class PhoneDtoMapper extends AbstractDtoMapper
{
	public function fromRecord(Phone $record): PhoneDto
	{
		return new PhoneDto([
			'phone'       => $record->phone,
			'exten'       => $record->exten,
			'type'        => $record->type,
			'isMain'      => $record->isMain,
			'countryCode' => $record->country_code,
			'comment'     => $record->comment,
			'contact'     => $record->contact
		]);
	}
}