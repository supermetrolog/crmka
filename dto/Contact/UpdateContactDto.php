<?php

namespace app\dto\Contact;

use yii\base\BaseObject;

class UpdateContactDto extends BaseObject
{
	public ?int    $consultant_id;
	public string  $first_name;
	public ?string $middle_name;
	public ?string $last_name;
	public ?int    $position;
	public ?int    $position_unknown;
	public ?int    $faceToFaceMeeting;
	public ?int    $warning;
	public ?int    $good;
	public ?int    $passive_why;
	public ?string $passive_why_comment;
	public ?string $warning_why_comment;
	public ?int    $isMain;
	public ?int    $status;

	public ?array $emails          = [];
	public ?array $websites        = [];
	public ?array $wayOfInformings = [];
}