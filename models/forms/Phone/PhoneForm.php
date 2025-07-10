<?php

declare(strict_types=1);

namespace app\models\forms\Phone;

use app\dto\Phone\PhoneDto;
use app\enum\Phone\PhoneCountryCodeEnum;
use app\enum\Phone\PhoneStatusEnum;
use app\enum\Phone\PhoneTypeEnum;
use app\helpers\validators\EnumValidator;
use app\kernel\common\models\Form\Form;
use app\models\User;
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
			[['phone'], 'required'],
			['phone', PhoneValidator::class],
			['isMain', 'integer'],
			[['phone', 'exten'], 'max' => 255],
			[['country_code', 'type', 'status'], 'string'],
			['status', EnumValidator::class, 'class' => PhoneStatusEnum::class],
			['type', EnumValidator::class, 'class' => PhoneTypeEnum::class],
			['country_code', EnumValidator::class, 'class' => PhoneCountryCodeEnum::class],
			[['comment'], 'string', 'max' => 128],
			['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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