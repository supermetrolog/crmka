<?php


namespace app\helpers\validators;

use app\helpers\ArrayHelper;
use app\helpers\PhoneHelper;
use libphonenumber\NumberParseException;
use yii\validators\Validator;


class PhoneValidator extends Validator
{
	public array $allowedRegions = ['RU', 'BY', 'UA'];
	private const defaultRegion = 'RU';

	public ?string $countryCode          = null;
	public ?string $countryCodeAttribute = null;

	private function getCountryCode($model): ?string
	{
		if (!empty($this->countryCode)) {
			return $this->countryCode;
		}

		if (empty($this->countryCodeAttribute)) {
			return self::defaultRegion;
		}

		return $model->{$this->countryCodeAttribute} ?? self::defaultRegion;
	}

	/**
	 * @throws NumberParseException
	 */
	public function validateAttribute($model, $attribute): void
	{
		$value = $model->$attribute;

		$isPossible = PhoneHelper::isPossibleNumber($value);

		if (!$isPossible) {
			$this->addError($model, $attribute, "{attribute} не является допустимым номером");

			return;
		}

		$countryCode = $this->getCountryCode($model);

		if ($countryCode) {
			if (!ArrayHelper::includes($this->allowedRegions, $countryCode)) {
				$this->addError(
					$model,
					$attribute,
					"\"{countryCode}\" не входит в список допустимых регионов",
					['countryCode' => $countryCode]
				);
			}

			$phoneNumber = PhoneHelper::parse($value);

			$isValid = PhoneHelper::isValidNumberForRegion($phoneNumber, $countryCode);

			if (!$isValid) {
				$this->addError(
					$model,
					$attribute,
					"{attribute} не соответствует региону \"{countryCode}\"",
					['countryCode' => $countryCode]
				);
			}
		}
	}
}
