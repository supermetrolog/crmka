<?php

declare(strict_types=1);

namespace app\models\forms\Deal;

use app\kernel\common\models\Form\Form;
use app\models\Company;
use app\models\Objects;
use app\models\oldDb\Complex;
use app\models\Request;
use app\models\User;

class DealForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $company_id;
	public $consultant_id;
	public $area;
	public $floorPrice;
	public $clientLegalEntity;
	public $description;
	public $name;
	public $object_id;
	public $complex_id;
	public $is_our;
	public $is_competitor;
	public $competitor_company_id;
	public $type_id;
	public $dealDate;
	public $contractTerm;
	public $formOfOrganization;
	public $original_id;
	public $visual_id;
	public $request_id;

	public function rules(): array
	{
		return [
			[['company_id', 'consultant_id', 'object_id', 'complex_id', 'type_id', 'original_id', 'visual_id', 'request_id'], 'required'],
			[['company_id', 'consultant_id', 'object_id', 'complex_id', 'type_id', 'original_id', 'request_id', 'area', 'floorPrice', 'competitor_company_id', 'contractTerm', 'formOfOrganization', 'is_our', 'is_competitor'], 'integer'],
			[['clientLegalEntity', 'description', 'name', 'visual_id'], 'string'],
			[['created_at', 'updated_at', 'dealDate'], 'safe'],
			['company_id', 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
			['request_id', 'exist', 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
			['consultant_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['consultant_id' => 'id']],
			['object_id', 'exist', 'targetClass' => Objects::class, 'targetAttribute' => ['object_id' => 'id']],
			['complex_id', 'exist', 'targetClass' => Complex::class, 'targetAttribute' => ['complex_id' => 'id']],
			['competitor_company_id', 'exist', 'targetClass' => Company::class, 'targetAttribute' => ['competitor_company_id' => 'id']],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'name',
			'object_id',
			'is_our',
			'is_competitor',
			'type_id',
			'clientLegalEntity',
			'description',
			'area',
			'floorPrice',
			'complex_id',
			'company_id',
			'original_id',
			'competitor_company_id',
			'dealDate',
			'contractTerm',
			'formOfOrganization',
			'visual_id',
			'consultant_id',
			'request_id',
		];

		return [
			self::SCENARIO_CREATE => [...$common],
			self::SCENARIO_UPDATE => [...$common],
		];
	}
}