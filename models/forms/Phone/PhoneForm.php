<?php

declare(strict_types=1);

namespace app\models\forms\Phone;

use app\dto\Phone\PhoneDto;
use app\enum\Phone\PhoneCountryCodeEnum;
use app\enum\Phone\PhoneTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\Form\Form;
use Exception;
use floor12\phone\PhoneValidator;

class PhoneForm extends Form
{
	public $phone;
	public $country_code = PhoneCountryCodeEnum::RU;
	public $exten;
	public $isMain;
	public $type         = PhoneTypeEnum::MOBILE;
	public $comment;

	public function rules(): array
	{
		return [
			['phone', 'required'],
			['phone', PhoneValidator::class],
			['isMain', 'integer'],
			['phone', 'string', 'max' => 255],
			[['country_code', 'type', 'exten'], 'string'],
			['type', EnumValidator::class, 'enumClass' => PhoneTypeEnum::class],
			['country_code', EnumValidator::class, 'enumClass' => PhoneCountryCodeEnum::class],
			['comment', 'string', 'max' => 128]
		];
	}

	public function attributeLabels(): array
	{
		return [
			'phone'       => 'Телефон',
			'countryCode' => 'Код страны',
			'exten'       => 'Добавочный номер',
			'isMain'      => 'Флаг основного телефона',
			'type'        => 'Тип',
			'comment'     => 'Комментарии',
		];
	}

	/**
	 * @throws Exception
	 */
	public function getDto(): PhoneDto
	{
		return new PhoneDto([
			'phone'       => $this->phone,
			'countryCode' => $this->country_code,
			'exten'       => $this->exten,
			'isMain'      => $this->isMain,
			'type'        => $this->type,
			'comment'     => $this->comment,
		]);
	}
}