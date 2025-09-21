<?php

declare(strict_types=1);

namespace app\models\forms\Contact;

use app\dto\Contact\CreateContactDto;
use app\dto\Contact\UpdateContactDto;
use app\helpers\ArrayHelper;
use app\helpers\validators\AnyValidator;
use app\kernel\common\models\Form\Form;
use app\models\Company\Company;
use app\models\ContactPosition;
use app\models\User\User;

class ContactForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $company_id;
	public $consultant_id;
	public $first_name;
	public $middle_name;
	public $last_name;
	public $position_id;
	public $position_unknown;
	public $faceToFaceMeeting;
	public $warning;
	public $good;
	public $passive_why;
	public $passive_why_comment;
	public $warning_why_comment;
	public $isMain;
	public $status;

	public $emails;
	public $phones;
	public $websites;
	public $wayOfInformings;

	public function rules(): array
	{
		return [
			[['company_id', 'first_name', 'consultant_id'], 'required'],
			[['company_id', 'status', 'consultant_id', 'position_id', 'faceToFaceMeeting', 'warning', 'good', 'passive_why', 'position_unknown', 'isMain'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['first_name', 'middle_name', 'last_name', 'passive_why_comment', 'warning_why_comment'], 'string', 'max' => 255],
			['company_id', 'exist', 'targetClass' => Company::class, 'targetAttribute' => 'id'],
			['consultant_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
			['position_id', 'exist', 'targetClass' => ContactPosition::class, 'targetAttribute' => 'id'],
			[
				['emails', 'phones'],
				AnyValidator::class,
				'message' => 'Контакт должен иметь хотя бы один Email или телефон',
				'rule'    => 'required'
			],
		];
	}

	public function scenarios(): array
	{
		$common = [
			'consultant_id',
			'first_name',
			'middle_name',
			'last_name',
			'position_id',
			'position_unknown',
			'faceToFaceMeeting',
			'warning',
			'good',
			'passive_why',
			'passive_why_comment',
			'warning_why_comment',
			'isMain',
			'status',
			'emails',
			'phones',
			'websites',
			'wayOfInformings'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'company_id'],
			self::SCENARIO_UPDATE => $common
		];
	}


	public function getDto()
	{
		$common = [
			'consultant_id'       => $this->consultant_id,
			'first_name'          => $this->first_name,
			'middle_name'         => $this->middle_name,
			'last_name'           => $this->last_name,
			'position_id'         => $this->position_id,
			'position_unknown'    => $this->position_unknown,
			'faceToFaceMeeting'   => $this->faceToFaceMeeting,
			'warning'             => $this->warning,
			'good'                => $this->good,
			'passive_why'         => $this->passive_why,
			'passive_why_comment' => $this->passive_why_comment,
			'warning_why_comment' => $this->warning_why_comment,
			'isMain'              => $this->isMain,
			'status'              => $this->status,
			'emails'              => $this->emails,
			'websites'            => $this->websites,
			'wayOfInformings'     => $this->wayOfInformings
		];

		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateContactDto(ArrayHelper::merge(
				$common,
				['company_id' => $this->company_id]
			));
		}

		return new UpdateContactDto($common);
	}
}